<?php
namespace App\Services;

use App\Models\Daybook;
use App\Models\DaybookPart;
use App\Models\Account;
use Illuminate\Support\Facades\DB;

class DaybookService
{
    // Get account by code, throw if not found
    private function getAccount(string $code): Account
    {
        $acc = Account::where('code', $code)->where('status', 1)->first();
        if (!$acc) throw new \Exception("Account not found: {$code}");
        return $acc;
    }

    // Insert a single daybook line
    private function insertLine(string $slno, Account $acc, float $amount, string $particular, string $tdate, string $vtype, ?int $branchId = null): void
    {
        if (round($amount, 4) == 0) return; // skip zero lines
        Daybook::create([
            'slno'         => $slno,
            'account_id'   => $acc->id,
            'account_code' => $acc->code,
            'account_name' => $acc->name,
            'amount'       => $amount,
            'particular'   => $particular,
            'tdate'        => $tdate,
            'vtype'        => $vtype,
            'branch_id'    => $branchId,
        ]);
    }

    // Validate that SUM(amount) for slno = 0
    public function validateBalance(string $slno): bool
    {
        $sum = Daybook::where('slno', $slno)->sum('amount');
        return round((float)$sum, 2) === 0.00;
    }

    // Add ROUND line if needed to make sum = 0
    private function addRoundEntry(string $slno, string $tdate, string $vtype, ?int $branchId): void
    {
        $sum = (float) Daybook::where('slno', $slno)->sum('amount');
        $diff = round($sum, 4);
        if ($diff != 0) {
            $round = $this->getAccount('ROUND');
            // negate diff so sum becomes 0
            $this->insertLine($slno, $round, -$diff, 'Round Off', $tdate, $vtype, $branchId);
        }
    }

    /**
     * SALES INVOICE daybook entries
     *
     * amount < 0 = Debit, amount > 0 = Credit
     *
     * Customer        -net_amount          Debit customer (they owe us)
     * Customer        +received_amount     Credit customer (they paid, reduces balance)
     * Cash/Bank       -received_amount     Debit cash/bank (we receive money)
     * Sales (RS)      +taxable_amount      Credit sales
     * Discount        -discount            Debit discount (cost to us)
     * SGST            +sgst                Credit SGST payable
     * CGST            +cgst                Credit CGST payable
     * IGST            +igst                Credit IGST payable
     * ROUND           balancing entry
     */
    public function insertSalesInvoiceDaybookEntries(\App\Models\SalesInvoice $inv): void
    {
        $slno    = $inv->slno;
        $date    = $inv->invoice_date;
        $vtype   = 'SI';
        $branch  = $inv->branch_id;
        $billRef = "By Sales ({$inv->invoice_no}) To {$inv->customer_name}";

        // Delete existing entries for this slno (for re-post)
        Daybook::where('slno', $slno)->delete();
        DaybookPart::where('slno', $slno)->delete();

        // DaybookPart header
        DaybookPart::create([
            'slno'       => $slno,
            'vchno'      => $inv->invoice_no,
            'particular' => $billRef,
            'tdate'      => $date,
            'vtype'      => $vtype,
            'branch_id'  => $branch,
            'created_by' => auth()->id(),
        ]);

        // Get accounts
        $customerAcc = Account::find($inv->customer_id) ?? $this->getAccount('CUST-DEFAULT');
        $salesAcc    = $this->getAccount('RS');
        $discAcc     = $this->getAccount('DISC');
        $sgstAcc     = $this->getAccount('SGST');
        $cgstAcc     = $this->getAccount('CGST');
        $igstAcc     = $this->getAccount('IGST');

        // 1. Debit customer for net amount (negative = debit)
        $this->insertLine($slno, $customerAcc, -$inv->net_amount, $billRef, $date, $vtype, $branch);

        // 2. Credit customer for received amount (if received)
        if ($inv->received_amount > 0) {
            $this->insertLine($slno, $customerAcc, $inv->received_amount, "Received against {$inv->invoice_no}", $date, $vtype, $branch);

            // 3. Debit cash/bank for received amount
            $cashBankAcc = $inv->payment_mode === 'cash'
                ? $this->getAccount('CASH')
                : ($inv->bank_account_id ? Account::find($inv->bank_account_id) : $this->getAccount('BANK'));
            $this->insertLine($slno, $cashBankAcc, -$inv->received_amount, "Cash/Bank Receipt {$inv->invoice_no}", $date, $vtype, $branch);
        }

        // 4. Credit sales (positive = credit)
        $this->insertLine($slno, $salesAcc, $inv->taxable_amount, "Sales {$inv->invoice_no}", $date, $vtype, $branch);

        // 5. Debit discount (negative = debit)
        if ($inv->discount > 0) {
            $this->insertLine($slno, $discAcc, -$inv->discount, "Discount on {$inv->invoice_no}", $date, $vtype, $branch);
        }

        // 6. Credit taxes
        if ($inv->sgst > 0) $this->insertLine($slno, $sgstAcc, $inv->sgst, "SGST {$inv->invoice_no}", $date, $vtype, $branch);
        if ($inv->cgst > 0) $this->insertLine($slno, $cgstAcc, $inv->cgst, "CGST {$inv->invoice_no}", $date, $vtype, $branch);
        if ($inv->igst > 0) $this->insertLine($slno, $igstAcc, $inv->igst, "IGST {$inv->invoice_no}", $date, $vtype, $branch);

        // 7. Add round-off entry if needed
        $this->addRoundEntry($slno, $date, $vtype, $branch);
    }

    /**
     * PURCHASE INVOICE daybook entries
     *
     * Purchase (EP)   -taxable_amount      Debit purchase/raw-material
     * SGST            -sgst                Debit input SGST
     * CGST            -cgst                Debit input CGST
     * IGST            -igst                Debit input IGST
     * Supplier        +net_amount          Credit supplier (we owe them)
     * Discount        +discount            Credit discount received
     * TCS             -tcs                 Debit TCS
     * Supplier        -paid_amount         Debit supplier (reduce liability)
     * Cash/Bank       +paid_amount         Credit cash/bank (we pay)
     * ROUND           balancing
     */
    public function insertPurchaseInvoiceDaybookEntries(\App\Models\PurchaseInvoice $inv): void
    {
        $slno    = $inv->slno;
        $date    = $inv->invoice_date;
        $vtype   = 'PI';
        $branch  = $inv->branch_id;
        $ref     = "By Purchase - {$inv->doc_no} - {$inv->supplier_bill_no} From {$inv->supplier_name}";

        Daybook::where('slno', $slno)->delete();
        DaybookPart::where('slno', $slno)->delete();

        DaybookPart::create([
            'slno'       => $slno,
            'vchno'      => $inv->doc_no,
            'particular' => $ref,
            'tdate'      => $date,
            'vtype'      => $vtype,
            'branch_id'  => $branch,
            'created_by' => auth()->id(),
        ]);

        $supplierAcc  = Account::find($inv->supplier_id) ?? $this->getAccount('SUPP-DEFAULT');
        $purchaseAcc  = $this->getAccount('EP');
        $sgstAcc      = $this->getAccount('SGST');
        $cgstAcc      = $this->getAccount('CGST');
        $igstAcc      = $this->getAccount('IGST');
        $discAcc      = $this->getAccount('DISC');
        $tcsAcc       = $this->getAccount('TCS');

        // 1. Debit purchase/RM account (negative = debit)
        $this->insertLine($slno, $purchaseAcc, -$inv->taxable_amount, "Purchase {$inv->doc_no}", $date, $vtype, $branch);

        // 2. Debit input GST
        if ($inv->sgst > 0) $this->insertLine($slno, $sgstAcc, -$inv->sgst, "Input SGST {$inv->doc_no}", $date, $vtype, $branch);
        if ($inv->cgst > 0) $this->insertLine($slno, $cgstAcc, -$inv->cgst, "Input CGST {$inv->doc_no}", $date, $vtype, $branch);
        if ($inv->igst > 0) $this->insertLine($slno, $igstAcc, -$inv->igst, "Input IGST {$inv->doc_no}", $date, $vtype, $branch);

        // 3. Credit supplier for net amount (positive = credit)
        $this->insertLine($slno, $supplierAcc, $inv->net_amount, $ref, $date, $vtype, $branch);

        // 4. Credit discount received
        if ($inv->discount > 0) {
            $this->insertLine($slno, $discAcc, $inv->discount, "Discount on {$inv->doc_no}", $date, $vtype, $branch);
        }

        // 5. Debit TCS
        if ($inv->tcs > 0) {
            $this->insertLine($slno, $tcsAcc, -$inv->tcs, "TCS {$inv->doc_no}", $date, $vtype, $branch);
        }

        // 6. If paid now: debit supplier (reduce liability) + credit cash/bank
        if ($inv->paid_amount > 0) {
            // Debit supplier (reduce liability, negative)
            $this->insertLine($slno, $supplierAcc, -$inv->paid_amount, "Payment against {$inv->doc_no}", $date, $vtype, $branch);

            // Credit cash/bank (positive = credit, money going out)
            $cashBankAcc = $inv->payment_mode === 'cash'
                ? $this->getAccount('CASH')
                : ($inv->bank_account_id ? Account::find($inv->bank_account_id) : $this->getAccount('BANK'));
            $this->insertLine($slno, $cashBankAcc, $inv->paid_amount, "Cash/Bank Payment {$inv->doc_no}", $date, $vtype, $branch);
        }

        $this->addRoundEntry($slno, $date, $vtype, $branch);
    }

    /**
     * RECEIPT VOUCHER daybook entries
     *
     * Party account   +(amount + discount)   Credit party (reduces receivable)
     * Cash/Bank       -amount                Debit cash/bank (we receive money)
     * Discount        -discount              Debit discount (if any)
     */
    public function insertReceiptDaybookEntries(\App\Models\Receipt $receipt): void
    {
        $slno   = $receipt->slno;
        $date   = $receipt->vch_date;
        $vtype  = 'RV';
        $branch = $receipt->branch_id;

        Daybook::where('slno', $slno)->delete();
        DaybookPart::where('slno', $slno)->delete();

        DaybookPart::create([
            'slno'        => $slno,
            'vchno'       => $receipt->vch_no,
            'particular'  => "Receipt from {$receipt->party_name}",
            'tdate'       => $date,
            'vtype'       => $vtype,
            'cheque_no'   => $receipt->cheque_no,
            'cheque_date' => $receipt->cheque_date,
            'bank_name'   => $receipt->bank_name,
            'branch_id'   => $branch,
            'created_by'  => auth()->id(),
        ]);

        $partyAcc    = Account::find($receipt->party_id) ?? $this->getAccount('CUST-DEFAULT');
        $cashBankAcc = $receipt->payment_mode === 'cash'
            ? $this->getAccount('CASH')
            : ($receipt->bank_account_id ? Account::find($receipt->bank_account_id) : $this->getAccount('BANK'));
        $discAcc     = $this->getAccount('DISC');

        $total = $receipt->amount + $receipt->discount;

        // 1. Credit party (positive = credit, reduces receivable)
        $this->insertLine($slno, $partyAcc, $total, "Receipt {$receipt->vch_no}", $date, $vtype, $branch);

        // 2. Debit cash/bank (negative = debit)
        $this->insertLine($slno, $cashBankAcc, -$receipt->amount, "Cash/Bank Receipt {$receipt->vch_no}", $date, $vtype, $branch);

        // 3. Debit discount (negative = debit)
        if ($receipt->discount > 0) {
            $this->insertLine($slno, $discAcc, -$receipt->discount, "Discount {$receipt->vch_no}", $date, $vtype, $branch);
        }

        $this->addRoundEntry($slno, $date, $vtype, $branch);
    }

    /**
     * PAYMENT VOUCHER daybook entries
     *
     * Cash/Bank       +amount    Credit cash/bank (money going out)
     * Party account   -amount    Debit party (reduces payable)
     */
    public function insertPaymentDaybookEntries(\App\Models\Payment $payment): void
    {
        $slno   = $payment->slno;
        $date   = $payment->vch_date;
        $vtype  = 'PV';
        $branch = $payment->branch_id;

        Daybook::where('slno', $slno)->delete();
        DaybookPart::where('slno', $slno)->delete();

        DaybookPart::create([
            'slno'        => $slno,
            'vchno'       => $payment->vch_no,
            'particular'  => "Payment to {$payment->party_name}",
            'tdate'       => $date,
            'vtype'       => $vtype,
            'cheque_no'   => $payment->cheque_no,
            'cheque_date' => $payment->cheque_date,
            'bank_name'   => $payment->bank_name,
            'branch_id'   => $branch,
            'created_by'  => auth()->id(),
        ]);

        $partyAcc    = Account::find($payment->party_id) ?? $this->getAccount('SUPP-DEFAULT');
        $cashBankAcc = $payment->payment_mode === 'cash'
            ? $this->getAccount('CASH')
            : ($payment->bank_account_id ? Account::find($payment->bank_account_id) : $this->getAccount('BANK'));

        // 1. Credit cash/bank (positive = credit, money going out)
        $this->insertLine($slno, $cashBankAcc, $payment->amount, "Cash/Bank Payment {$payment->vch_no}", $date, $vtype, $branch);

        // 2. Debit party (negative = debit, reduces payable)
        $this->insertLine($slno, $partyAcc, -$payment->amount, "Payment {$payment->vch_no}", $date, $vtype, $branch);

        $this->addRoundEntry($slno, $date, $vtype, $branch);
    }

    /**
     * Get ledger for an account with running balance
     */
    public function getLedger(int $accountId, string $fromDate, string $toDate): array
    {
        $account = Account::findOrFail($accountId);
        $lines   = Daybook::where('account_id', $accountId)
            ->whereBetween('tdate', [$fromDate, $toDate])
            ->orderBy('tdate')
            ->orderBy('id')
            ->get();

        $runningBalance = (float) $account->opening_balance;
        if ($account->ob_type === 'cr') $runningBalance = -$runningBalance;

        $rows = [];
        foreach ($lines as $line) {
            $runningBalance += (float) $line->amount;
            $rows[] = [
                'date'       => $line->tdate,
                'slno'       => $line->slno,
                'vtype'      => $line->vtype,
                'particular' => $line->particular,
                'debit'      => $line->amount < 0 ? abs($line->amount) : 0,
                'credit'     => $line->amount > 0 ? $line->amount : 0,
                'balance'    => $runningBalance,
                'dr_cr'      => $runningBalance >= 0 ? 'Dr' : 'Cr',
            ];
        }
        return ['account' => $account, 'rows' => $rows, 'closing_balance' => $runningBalance];
    }

    /**
     * Generate sequence number: PREFIX/YYMM/0001
     */
    public function generateSlno(string $prefix, string $model): string
    {
        $ym   = date('ym');
        $like = "{$prefix}/{$ym}/%";
        $last = DB::table(app($model)->getTable())
            ->where('slno', 'like', $like)
            ->orderByDesc('slno')
            ->value('slno');
        $seq  = $last ? (int) substr($last, -4) + 1 : 1;
        return sprintf('%s/%s/%04d', $prefix, $ym, $seq);
    }

    /**
     * Generate next sequence number (alias, financial year format)
     */
    public function nextSlno(string $prefix, string $table, string $column = 'slno'): string
    {
        $ym   = date('ym');
        $like = "{$prefix}/{$ym}/%";
        $last = DB::table($table)
            ->where($column, 'like', $like)
            ->orderByDesc($column)
            ->value($column);
        $seq = $last ? (int) substr($last, -4) + 1 : 1;
        return sprintf('%s/%s/%04d', $prefix, $ym, $seq);
    }
}
