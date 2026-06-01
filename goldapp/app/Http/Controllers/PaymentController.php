<?php
namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Payment;
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
        $payments = Payment::when($q, fn($qry) => $qry->where('party_name','like',"%$q%")->orWhere('vchno','like',"%$q%"))
            ->when($from, fn($qry) => $qry->where('vchdate','>=',$from))
            ->when($to,   fn($qry) => $qry->where('vchdate','<=',$to))
            ->orderBy('vchdate','desc')->orderBy('id','desc')
            ->paginate(20)->withQueryString();
        return view('payment.index', compact('payments','q','from','to'));
    }

    public function create()
    {
        $parties      = Account::whereIn('atype',['customer','supplier'])->orderBy('name')->get();
        $bankAccounts = Account::where('atype','bank')->orderBy('name')->get();
        $nextSlno     = $this->daybookService->nextSlno('PV', 'payment');
        return view('payment.create', compact('parties','bankAccounts','nextSlno'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'party_id' => 'required|exists:account,id',
            'vchdate'  => 'required|date',
            'amount'   => 'required|numeric|min:0.01',
        ]);

        DB::transaction(function () use ($request) {
            $slno  = $this->daybookService->nextSlno('PV', 'payment');
            $party = Account::find($request->party_id);

            $payment = Payment::create([
                'slno'            => $slno,
                'vchno'           => $slno,
                'vchdate'         => $request->vchdate,
                'party_id'        => $request->party_id,
                'party_name'      => $party->name,
                'amount'          => $request->amount,
                'payment_mode'    => $request->payment_mode ?? 'cash',
                'bank_account_id' => $request->bank_account_id ?? null,
                'cheque_no'       => $request->cheque_no,
                'cheque_date'     => $request->cheque_date ?: null,
                'bank_name'       => $request->bank_name,
                'narration'       => $request->narration,
                'status'          => 1,
                'created_by'      => Auth::id(),
            ]);

            $this->daybookService->insertPaymentDaybookEntries($payment);
        });

        return redirect()->route('payment.index')->with('success','Payment voucher saved.');
    }

    public function show(Payment $payment)
    {
        return view('payment.show', compact('payment'));
    }

    public function destroy(Payment $payment)
    {
        DB::transaction(function () use ($payment) {
            \App\Models\Daybook::where('slno', $payment->slno)->delete();
            \App\Models\Daybookpart::where('slno', $payment->slno)->delete();
            $payment->delete();
        });
        return redirect()->route('payment.index')->with('success','Payment deleted.');
    }
}
