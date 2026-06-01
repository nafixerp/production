<?php
namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Daybook;
use App\Models\DaybookPart;
use App\Models\FgStock;
use App\Models\FinishedGoods;
use App\Models\PosBill;
use App\Models\PosBillItem;
use App\Models\PosSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class POSController extends Controller
{
    public function index(Request $request)
    {
        // Show sessions list and open-session form
        $sessions = PosSession::with('cashier')
            ->orderByDesc('id')
            ->paginate(20)->withQueryString();

        $openSession = PosSession::where('cashier_id', Auth::id())
            ->where('status', 'open')
            ->first();

        return view('sales.pos.index', compact('sessions', 'openSession'));
    }

    /** Open a new POS session */
    public function store(Request $request)
    {
        $request->validate([
            'opening_cash' => 'required|numeric|min:0',
            'terminal'     => 'nullable|string|max:50',
        ]);

        // Close any open session for this cashier
        PosSession::where('cashier_id', Auth::id())->where('status', 'open')
            ->update(['status' => 'closed', 'closed_at' => now()]);

        $last = PosSession::orderByDesc('id')->value('session_no');
        $num  = $last ? (int) substr($last, 3) + 1 : 1;
        $sessionNo = 'POS' . str_pad($num, 5, '0', STR_PAD_LEFT);

        $session = PosSession::create([
            'session_no'   => $sessionNo,
            'cashier_id'   => Auth::id(),
            'terminal'     => $request->terminal ?? 'MAIN',
            'opened_at'    => now(),
            'opening_cash' => $request->opening_cash,
            'total_sales'  => 0,
            'status'       => 'open',
        ]);

        return redirect()->route('pos.bill', $session->id)->with('success', 'Session opened.');
    }

    /** POS quick-bill screen */
    public function bill(PosSession $posSession)
    {
        if ($posSession->status !== 'open') {
            return redirect()->route('pos.index')->with('error', 'Session closed.');
        }
        $fgList = FinishedGoods::where('status', 1)->orderBy('name')->get();
        $bills  = PosBill::where('session_id', $posSession->id)->orderByDesc('id')->get();
        return view('sales.pos.bill', compact('posSession', 'fgList', 'bills'));
    }

    /** Save a quick bill */
    public function saveBill(Request $request, PosSession $posSession)
    {
        $request->validate([
            'received_amount' => 'required|numeric|min:0',
            'items'           => 'required|array|min:1',
        ]);

        DB::transaction(function () use ($request, $posSession) {
            $items = $request->items ?? [];
            $taxable  = collect($items)->sum(fn($i) => (float)($i['amount'] ?? 0));
            $disc     = (float)($request->discount ?? 0);
            $sgst     = collect($items)->sum(fn($i) => (float)($i['sgst'] ?? 0));
            $cgst     = collect($items)->sum(fn($i) => (float)($i['cgst'] ?? 0));
            $net      = $taxable - $disc + $sgst + $cgst;
            $received = (float)$request->received_amount;
            $change   = max(0, $received - $net);

            $last = PosBill::orderByDesc('id')->value('slno');
            $num  = $last ? (int) substr($last, 4) + 1 : 1;
            $slno = 'BILL' . str_pad($num, 6, '0', STR_PAD_LEFT);

            $bill = PosBill::create([
                'slno'           => $slno,
                'bill_no'        => $slno,
                'bill_date'      => now()->toDateString(),
                'session_id'     => $posSession->id,
                'customer_id'    => $request->customer_id ?: null,
                'customer_name'  => $request->customer_name ?? 'Walk-in',
                'taxable_amount' => $taxable,
                'discount'       => $disc,
                'sgst'           => $sgst,
                'cgst'           => $cgst,
                'net_amount'     => $net,
                'received_amount'=> $received,
                'change_amount'  => $change,
                'payment_mode'   => $request->payment_mode ?? 'cash',
                'branch_id'      => Auth::user()->branch_id ?? null,
                'cashier_id'     => Auth::id(),
            ]);

            foreach ($items as $item) {
                if (empty($item['fg_id'])) continue;
                $fg  = FinishedGoods::find($item['fg_id']);
                $qty = (float)($item['qty'] ?? 0);
                $rate = (float)($item['rate'] ?? $fg?->selling_price ?? 0);
                $discPct = (float)($item['discount_pct'] ?? 0);
                $amt  = $qty * $rate * (1 - $discPct / 100);
                $sgstAmt = $amt * (float)($item['sgst_pct'] ?? 0) / 100;
                $cgstAmt = $amt * (float)($item['cgst_pct'] ?? 0) / 100;
                PosBillItem::create([
                    'bill_id'     => $bill->id,
                    'fg_id'       => $item['fg_id'],
                    'fg_code'     => $fg?->code ?? '',
                    'fg_name'     => $fg?->name ?? $item['fg_name'] ?? '',
                    'qty'         => $qty,
                    'rate'        => $rate,
                    'discount_pct'=> $discPct,
                    'amount'      => $amt,
                    'sgst'        => $sgstAmt,
                    'cgst'        => $cgstAmt,
                    'net_amount'  => $amt + $sgstAmt + $cgstAmt,
                    'batch_no'    => $item['batch_no'] ?? null,
                ]);

                // Reduce stock (FIFO: earliest expiry first)
                if ($qty > 0) {
                    $stockRows = FgStock::where('fg_id', $item['fg_id'])
                        ->where('status', 'available')
                        ->orderBy('expiry_date')
                        ->get();
                    $remaining = $qty;
                    foreach ($stockRows as $row) {
                        if ($remaining <= 0) break;
                        $avail = $row->qty_in - $row->qty_out - $row->qty_reserved;
                        $take  = min($avail, $remaining);
                        $row->increment('qty_out', $take);
                        $remaining -= $take;
                    }
                }
            }

            // Update session total
            $posSession->increment('total_sales', $net);

            // Post simple daybook for cash sale
            $cashAcc  = Account::where('code', 'CASH')->orWhere('atype', 'CASH')->first();
            $salesAcc = Account::where('code', 'RS')->orWhere('code', 'SALES')->first();
            if ($cashAcc && $salesAcc) {
                $ref = "POS Bill {$slno}";
                Daybook::create([
                    'slno' => $slno, 'account_id' => $cashAcc->id,
                    'account_code' => $cashAcc->code, 'account_name' => $cashAcc->name,
                    'amount' => -$received, 'particular' => "Cash/Card – $ref",
                    'tdate' => $bill->bill_date, 'vtype' => 'POS', 'branch_id' => $bill->branch_id,
                ]);
                Daybook::create([
                    'slno' => $slno, 'account_id' => $salesAcc->id,
                    'account_code' => $salesAcc->code, 'account_name' => $salesAcc->name,
                    'amount' => $taxable, 'particular' => "POS Sales – $ref",
                    'tdate' => $bill->bill_date, 'vtype' => 'POS', 'branch_id' => $bill->branch_id,
                ]);
                DaybookPart::create([
                    'slno' => $slno, 'vchno' => $slno,
                    'particular' => $ref, 'tdate' => $bill->bill_date,
                    'vtype' => 'POS', 'branch_id' => $bill->branch_id, 'created_by' => Auth::id(),
                ]);
            }
        });

        return redirect()->route('pos.bill', $posSession)->with('success', 'Bill saved.');
    }

    /** Close session with cash reconciliation */
    public function closeSession(PosSession $posSession)
    {
        $request = request();
        DB::transaction(function () use ($posSession, $request) {
            $posSession->update([
                'closed_at'    => now(),
                'closing_cash' => $request->closing_cash ?? $posSession->total_sales,
                'status'       => 'closed',
            ]);
        });
        return redirect()->route('pos.index')->with('success', 'Session closed.');
    }

    public function show(PosSession $posSession)
    {
        $posSession->load('bills.items');
        return view('sales.pos.show', compact('posSession'));
    }

    public function create()
    {
        return redirect()->route('pos.index');
    }

    public function edit(PosSession $posSession) { return redirect()->route('pos.index'); }
    public function update(Request $request, PosSession $posSession) { return redirect()->route('pos.index'); }
    public function destroy(PosSession $posSession) { return redirect()->route('pos.index'); }
}
