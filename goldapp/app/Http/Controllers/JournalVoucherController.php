<?php
namespace App\Http\Controllers;

use App\Models\JournalVoucher;
use App\Models\JournalVoucherLine;
use App\Models\ChartOfAccount;
use App\Models\Daybook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JournalVoucherController extends Controller
{
    public function index(Request $request)
    {
        $q = JournalVoucher::query();
        if ($request->status) $q->where('status', $request->status);
        if ($request->voucher_type) $q->where('voucher_type', $request->voucher_type);
        if ($request->from) $q->where('jv_date', '>=', $request->from);
        if ($request->to) $q->where('jv_date', '<=', $request->to);
        $vouchers = $q->orderBy('jv_date','desc')->paginate(25)->withQueryString();
        return view('finance.journal-vouchers.index', compact('vouchers'));
    }

    public function create()
    {
        $slno = 'JV-' . date('Ymd') . '-' . str_pad(JournalVoucher::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);
        $accounts = ChartOfAccount::where('status',1)->where('allow_direct_posting',1)->orderBy('name')->get();
        $branches = \App\Models\Branch::where('status',1)->get();
        return view('finance.journal-vouchers.form', compact('slno','accounts','branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'slno'         => 'required|string|max:30|unique:journal_vouchers,slno',
            'jv_date'      => 'required|date',
            'voucher_type' => 'required|in:JV,CO,DN,CN',
            'narration'    => 'nullable|string',
            'branch_id'    => 'nullable|integer',
            'lines'        => 'required|array|min:2',
            'lines.*.account_id' => 'required|integer',
            'lines.*.amount'     => 'required|numeric',
            'lines.*.particular' => 'nullable|string|max:255',
            'lines.*.cost_centre'=> 'nullable|string|max:80',
        ]);

        $lines = $request->lines;
        // Double-entry validation: sum of all signed amounts must = 0
        $sum = array_sum(array_column($lines, 'amount'));
        if (abs($sum) > 0.01) {
            return back()->withInput()->withErrors([
                'lines' => 'Journal entry is not balanced. Sum of all lines must be 0. Current sum: ' . number_format($sum, 4)
            ]);
        }

        DB::transaction(function() use ($request, $lines) {
            $totalDebit = $totalCredit = 0;
            foreach ($lines as $line) {
                $amt = (float)$line['amount'];
                if ($amt < 0) $totalDebit += abs($amt);
                else $totalCredit += $amt;
            }

            $jv = JournalVoucher::create([
                'slno'         => $request->slno,
                'jv_no'        => $request->slno,
                'jv_date'      => $request->jv_date,
                'voucher_type' => $request->voucher_type,
                'narration'    => $request->narration,
                'total_debit'  => $totalDebit,
                'total_credit' => $totalCredit,
                'status'       => 'draft',
                'branch_id'    => $request->branch_id,
                'created_by'   => auth()->id(),
            ]);

            foreach ($lines as $line) {
                $account = ChartOfAccount::find($line['account_id']);
                JournalVoucherLine::create([
                    'jv_id'        => $jv->id,
                    'account_id'   => $line['account_id'],
                    'account_code' => $account->code ?? '',
                    'account_name' => $account->name ?? '',
                    'amount'       => $line['amount'],
                    'particular'   => $line['particular'] ?? $request->narration,
                    'cost_centre'  => $line['cost_centre'] ?? null,
                ]);
            }
        });

        return redirect()->route('journal-vouchers.index')->with('success', 'Journal voucher saved as draft.');
    }

    public function show($id)
    {
        $voucher = JournalVoucher::with('lines.account')->findOrFail($id);
        $daybookEntries = Daybook::where('slno', $voucher->slno)->get();
        return view('finance.journal-vouchers.show', compact('voucher','daybookEntries'));
    }

    public function edit($id)
    {
        $voucher = JournalVoucher::with('lines')->findOrFail($id);
        if ($voucher->status === 'posted') {
            return back()->with('error', 'Cannot edit a posted voucher.');
        }
        $accounts = ChartOfAccount::where('status',1)->where('allow_direct_posting',1)->orderBy('name')->get();
        $branches = \App\Models\Branch::where('status',1)->get();
        return view('finance.journal-vouchers.form', compact('voucher','accounts','branches'));
    }

    public function update(Request $request, $id)
    {
        $voucher = JournalVoucher::findOrFail($id);
        if ($voucher->status === 'posted') {
            return back()->with('error', 'Cannot edit a posted voucher.');
        }

        $request->validate([
            'slno'         => "required|string|max:30|unique:journal_vouchers,slno,{$id}",
            'jv_date'      => 'required|date',
            'voucher_type' => 'required|in:JV,CO,DN,CN',
            'lines'        => 'required|array|min:2',
            'lines.*.account_id' => 'required|integer',
            'lines.*.amount'     => 'required|numeric',
        ]);

        $lines = $request->lines;
        $sum = array_sum(array_column($lines, 'amount'));
        if (abs($sum) > 0.01) {
            return back()->withInput()->withErrors(['lines' => 'Journal entry is not balanced. Sum = ' . number_format($sum, 4)]);
        }

        DB::transaction(function() use ($request, $lines, $voucher) {
            $totalDebit = $totalCredit = 0;
            foreach ($lines as $line) {
                $amt = (float)$line['amount'];
                if ($amt < 0) $totalDebit += abs($amt);
                else $totalCredit += $amt;
            }

            $voucher->update([
                'slno'         => $request->slno,
                'jv_date'      => $request->jv_date,
                'voucher_type' => $request->voucher_type,
                'narration'    => $request->narration,
                'total_debit'  => $totalDebit,
                'total_credit' => $totalCredit,
                'branch_id'    => $request->branch_id,
            ]);

            $voucher->lines()->delete();
            foreach ($lines as $line) {
                $account = ChartOfAccount::find($line['account_id']);
                JournalVoucherLine::create([
                    'jv_id'        => $voucher->id,
                    'account_id'   => $line['account_id'],
                    'account_code' => $account->code ?? '',
                    'account_name' => $account->name ?? '',
                    'amount'       => $line['amount'],
                    'particular'   => $line['particular'] ?? $request->narration,
                    'cost_centre'  => $line['cost_centre'] ?? null,
                ]);
            }
        });

        return redirect()->route('journal-vouchers.index')->with('success', 'Journal voucher updated.');
    }

    public function destroy($id)
    {
        $voucher = JournalVoucher::findOrFail($id);
        if ($voucher->status === 'posted') {
            return back()->with('error', 'Cannot delete a posted voucher.');
        }
        $voucher->delete();
        return redirect()->route('journal-vouchers.index')->with('success', 'Voucher deleted.');
    }

    public function approve($id)
    {
        $voucher = JournalVoucher::findOrFail($id);
        if ($voucher->status !== 'draft') {
            return back()->with('error', 'Only draft vouchers can be approved.');
        }
        $voucher->update(['status' => 'approved', 'approved_by' => auth()->id()]);
        return back()->with('success', 'Voucher approved.');
    }

    // Post voucher to daybook (double-entry)
    public function post($id)
    {
        $voucher = JournalVoucher::with('lines')->findOrFail($id);
        if ($voucher->status === 'posted') {
            return back()->with('error', 'Voucher already posted.');
        }

        $sum = $voucher->lines->sum('amount');
        if (abs($sum) > 0.01) {
            return back()->with('error', 'Voucher is unbalanced (sum = ' . $sum . '). Cannot post.');
        }

        DB::transaction(function() use ($voucher) {
            // Remove existing daybook entries for idempotency
            Daybook::where('slno', $voucher->slno)->where('vtype', $voucher->voucher_type)->delete();

            foreach ($voucher->lines as $line) {
                // amount < 0 = Debit, amount > 0 = Credit (convention from spec)
                Daybook::create([
                    'slno'         => $voucher->slno,
                    'account_id'   => $line->account_id,
                    'account_code' => $line->account_code,
                    'account_name' => $line->account_name,
                    'amount'       => $line->amount,
                    'particular'   => $line->particular ?? $voucher->narration,
                    'tdate'        => $voucher->jv_date->toDateString(),
                    'vtype'        => $voucher->voucher_type,
                    'branch_id'    => $voucher->branch_id,
                ]);
            }

            $voucher->update(['status' => 'posted']);
        });

        return back()->with('success', 'Voucher posted to daybook successfully.');
    }
}
