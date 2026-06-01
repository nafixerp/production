<?php
namespace App\Http\Controllers;

use App\Models\BankReconciliation;
use App\Models\BankReconLine;
use App\Models\ChartOfAccount;
use App\Models\Daybook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BankReconciliationController extends Controller
{
    public function index(Request $request)
    {
        $reconciliations = BankReconciliation::with('bankAccount')
            ->orderBy('statement_date','desc')->paginate(20);
        $bankAccounts = ChartOfAccount::where('is_bank_account', 1)->where('status',1)->get();
        return view('finance.bank-reconciliation.index', compact('reconciliations','bankAccounts'));
    }

    public function create()
    {
        $bankAccounts = ChartOfAccount::where('is_bank_account', 1)->where('status',1)->get();
        return view('finance.bank-reconciliation.form', compact('bankAccounts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'bank_account_id'            => 'required|integer',
            'statement_date'             => 'required|date',
            'statement_closing_balance'  => 'required|numeric',
        ]);

        // Calculate book balance from daybook
        $account = ChartOfAccount::findOrFail($data['bank_account_id']);
        $bookBalance = $account->opening_balance + Daybook::where('account_id', $data['bank_account_id'])
            ->where('tdate', '<=', $data['statement_date'])
            ->sum('amount');

        $recon = BankReconciliation::create([
            'bank_account_id'           => $data['bank_account_id'],
            'statement_date'            => $data['statement_date'],
            'statement_closing_balance' => $data['statement_closing_balance'],
            'book_balance'              => $bookBalance,
            'difference'                => $data['statement_closing_balance'] - $bookBalance,
            'status'                    => 'in_progress',
        ]);

        // Auto-load daybook entries as recon lines
        $entries = Daybook::where('account_id', $data['bank_account_id'])
            ->where('tdate', '<=', $data['statement_date'])
            ->orderBy('tdate','asc')->get();

        foreach ($entries as $entry) {
            BankReconLine::create([
                'recon_id'      => $recon->id,
                'tdate'         => $entry->tdate,
                'description'   => $entry->particular,
                'amount'        => abs($entry->amount),
                'dr_cr'         => $entry->amount < 0 ? 'dr' : 'cr',
                'daybook_id'    => $entry->id,
                'is_matched'    => 0,
                'statement_line'=> 0,
            ]);
        }

        return redirect()->route('bank-reconciliation.show', $recon->id)->with('success', 'Bank reconciliation started.');
    }

    public function show($id)
    {
        $recon = BankReconciliation::with(['bankAccount','lines'])->findOrFail($id);
        $bookLines = $recon->lines->where('statement_line', 0);
        $statementLines = $recon->lines->where('statement_line', 1);
        $matchedBalance = $recon->lines->where('is_matched', 1)->where('statement_line', 0)
            ->sum(fn($l) => $l->dr_cr === 'cr' ? $l->amount : -$l->amount);
        return view('finance.bank-reconciliation.reconcile', compact('recon','bookLines','statementLines','matchedBalance'));
    }

    public function edit($id)
    {
        $recon = BankReconciliation::with('lines')->findOrFail($id);
        $bankAccounts = ChartOfAccount::where('is_bank_account', 1)->where('status',1)->get();
        return view('finance.bank-reconciliation.form', compact('recon','bankAccounts'));
    }

    public function update(Request $request, $id)
    {
        $recon = BankReconciliation::findOrFail($id);
        // Add statement lines from manual input
        if ($request->statement_lines) {
            foreach ($request->statement_lines as $line) {
                if (empty($line['description'])) continue;
                BankReconLine::create([
                    'recon_id'       => $recon->id,
                    'tdate'          => $line['tdate'],
                    'description'    => $line['description'],
                    'amount'         => abs($line['amount']),
                    'dr_cr'          => $line['dr_cr'],
                    'statement_line' => 1,
                    'is_matched'     => 0,
                ]);
            }
        }

        // Match lines
        if ($request->match_ids) {
            BankReconLine::whereIn('id', $request->match_ids)->update(['is_matched' => 1]);
        }

        // Check if fully reconciled
        $unmatchedBook = BankReconLine::where('recon_id', $id)->where('statement_line', 0)->where('is_matched', 0)->count();
        if ($unmatchedBook === 0 && abs($recon->difference) < 0.01) {
            $recon->update([
                'status'          => 'reconciled',
                'reconciled_by'   => auth()->id(),
                'reconciled_at'   => now(),
            ]);
        }

        return back()->with('success', 'Reconciliation updated.');
    }

    public function destroy($id)
    {
        BankReconciliation::findOrFail($id)->delete();
        return redirect()->route('bank-reconciliation.index')->with('success', 'Reconciliation deleted.');
    }
}
