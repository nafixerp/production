<?php
namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Payment;
use App\Models\Daybook;
use App\Models\DaybookPart;
use App\Services\DaybookService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    protected DaybookService $daybookService;

    public function __construct(DaybookService $daybookService)
    {
        $this->daybookService = $daybookService;
    }

    public function index(Request $request)
    {
        $q        = $request->q;
        $from     = $request->from;
        $to       = $request->to;
        $payments = Payment::when($q, fn($qry) => $qry->where('party_name', 'like', "%$q%")->orWhere('vch_no', 'like', "%$q%"))
            ->when($from, fn($qry) => $qry->where('vch_date', '>=', $from))
            ->when($to,   fn($qry) => $qry->where('vch_date', '<=', $to))
            ->orderBy('vch_date', 'desc')->orderBy('id', 'desc')
            ->paginate(20)->withQueryString();
        return view('payment.index', compact('payments', 'q', 'from', 'to'));
    }

    public function create()
    {
        $parties      = Account::whereIn('atype', ['CUSTOMER', 'SUPPLIER'])->orderBy('name')->get();
        $bankAccounts = Account::where('atype', 'BANK')->orderBy('name')->get();
        $nextSlno     = $this->daybookService->nextSlno('PV', 'payment');
        return view('payment.create', compact('parties', 'bankAccounts', 'nextSlno'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'party_id' => 'required|exists:account,id',
            'vch_date' => 'required|date',
            'amount'   => 'required|numeric|min:0.01',
        ]);

        DB::transaction(function () use ($request) {
            $slno  = $this->daybookService->nextSlno('PV', 'payment');
            $party = Account::findOrFail($request->party_id);

            $payment = Payment::create([
                'slno'            => $slno,
                'vch_no'          => $slno,
                'vch_date'        => $request->vch_date,
                'party_id'        => $request->party_id,
                'party_name'      => $party->name,
                'amount'          => $request->amount,
                'payment_mode'    => $request->payment_mode ?? 'cash',
                'bank_account_id' => $request->bank_account_id ?: null,
                'cheque_no'       => $request->cheque_no,
                'cheque_date'     => $request->cheque_date ?: null,
                'bank_name'       => $request->bank_name,
                'narration'       => $request->narration,
                'status'          => 1,
                'created_by'      => Auth::id(),
            ]);

            $this->daybookService->insertPaymentDaybookEntries($payment);
        });

        return redirect()->route('payments.index')->with('success', 'Payment voucher saved.');
    }

    public function show(Payment $payment)
    {
        $daybookEntries = Daybook::where('slno', $payment->slno)->get();
        return view('payment.show', compact('payment', 'daybookEntries'));
    }

    public function edit(Payment $payment)
    {
        $parties      = Account::whereIn('atype', ['CUSTOMER', 'SUPPLIER'])->orderBy('name')->get();
        $bankAccounts = Account::where('atype', 'BANK')->orderBy('name')->get();
        return view('payment.edit', compact('payment', 'parties', 'bankAccounts'));
    }

    public function update(Request $request, Payment $payment)
    {
        $request->validate([
            'party_id' => 'required|exists:account,id',
            'vch_date' => 'required|date',
            'amount'   => 'required|numeric|min:0.01',
        ]);

        DB::transaction(function () use ($request, $payment) {
            $party = Account::findOrFail($request->party_id);

            $payment->update([
                'vch_date'        => $request->vch_date,
                'party_id'        => $request->party_id,
                'party_name'      => $party->name,
                'amount'          => $request->amount,
                'payment_mode'    => $request->payment_mode ?? 'cash',
                'bank_account_id' => $request->bank_account_id ?: null,
                'cheque_no'       => $request->cheque_no,
                'cheque_date'     => $request->cheque_date ?: null,
                'bank_name'       => $request->bank_name,
                'narration'       => $request->narration,
            ]);

            $this->daybookService->insertPaymentDaybookEntries($payment);
        });

        return redirect()->route('payments.index')->with('success', 'Payment updated.');
    }

    public function destroy(Payment $payment)
    {
        DB::transaction(function () use ($payment) {
            Daybook::where('slno', $payment->slno)->delete();
            DaybookPart::where('slno', $payment->slno)->delete();
            $payment->delete();
        });
        return redirect()->route('payments.index')->with('success', 'Payment deleted.');
    }
}
