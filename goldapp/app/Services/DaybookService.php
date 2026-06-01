<?php
namespace App\Services;

use App\Models\Account;
use App\Models\Daybook;
use App\Models\Daybookpart;
use Illuminate\Support\Facades\DB;

class DaybookService
{
    /**
     * Get account ID by code, return null if not found
     */
    protected function getAccountId(string $code): ?int
    {
        $acc = Account::where('code', $code)->first();
        return $acc ? $acc->id : null;
    }

    /**
     * Delete existing daybook entries for a slno and re-insert
     */
    protected function clearEntries(string $slno): void
    {
        Daybook::where('slno', $slno)->delete();
    }

    /**
     * Insert a single daybook line
     */
    protected function insertLine(string $slno, string $accountCode, float $amount, string $particular, string $date, string $vtype): void
    {
        if ($amount == 0) return;
        $accountId = $this->getAccountId($accountCode);
        Daybook::create([
            'slno'         => $slno,
            'account_id'   => $accountId,
            'account_code' => $accountCode,
            'amount'       => $amount,
            'particular'   => $particular,
            'tdate'        => $date,
            'vtype'        => $vtype,
        ]);
    }

    /**
     * Sales Bill Daybook Entries
     * Rule: negative = Debit, positive = Credit, SUM(amount) must = 0
     *
     * Customer    -net_amount       (Debit customer - they owe us)
     * Customer    +received_amount  (Credit customer - partial receipt)
     * Cash/Bank   -received_amount  (Debit cash/bank - we receive cash)
     * RS (Sales)  +gross_amount     (Credit sales)
     * DISC        -discount         (Debit discount given)
     * SGST        +sgst             (Credit SGST payable)
     * CGST        +cgst             (Credit CGST payable)
     * IGST        +igst             (Credit IGST payable)
     * HMC         +hmc              (Credit making charges income)
     * TCS         +tcs              (Credit TCS payable)
     * EP (Exch)   -exchange_amount  (Debit exchange - old gold received)
     * ESR         -sales_return     (Debit sales return)
     * ROUND       balancing entry   (make SUM = 0)
     */
    public function insertSalesDaybookEntries($salesm): void
    {
        DB::transaction(function () use ($salesm) {
            $slno    = $salesm->slno;
            $date    = $salesm->billdate;
            $vtype   = 'SALES';
            $partic  = 'Sales Bill: ' . $salesm->billno . ' - ' . $salesm->customer_name;

            $this->clearEntries($slno);

            // Determine cash/bank account code
            $cashBankCode = 'CASH';
            if (in_array($salesm->payment_mode, ['bank'])) {
                $bankAcc = Account::find($salesm->bank_account_id);
                $cashBankCode = $bankAcc ? $bankAcc->code : 'BANK';
            }

            // Customer debit (negative = debit customer for full bill)
            $this->insertLine($slno, $salesm->customer_id ? Account::find($salesm->customer_id)?->code ?? 'CUST' : 'CUST',
                -$salesm->net_amount, $partic, $date, $vtype);

            // If received, credit customer and debit cash/bank
            if ($salesm->received_amount > 0) {
                $custCode = $salesm->customer_id ? (Account::find($salesm->customer_id)?->code ?? 'CUST') : 'CUST';
                $this->insertLine($slno, $custCode, $salesm->received_amount, $partic, $date, $vtype);
                $this->insertLine($slno, $cashBankCode, -$salesm->received_amount, $partic, $date, $vtype);
            }

            // Credit sales account
            $this->insertLine($slno, 'RS', $salesm->gross_amount, $partic, $date, $vtype);

            // Debit discount given
            if ($salesm->discount > 0) {
                $this->insertLine($slno, 'DISC', -$salesm->discount, $partic, $date, $vtype);
            }

            // Credit GST
            if ($salesm->sgst > 0) $this->insertLine($slno, 'SGST', $salesm->sgst, $partic, $date, $vtype);
            if ($salesm->cgst > 0) $this->insertLine($slno, 'CGST', $salesm->cgst, $partic, $date, $vtype);
            if ($salesm->igst > 0) $this->insertLine($slno, 'IGST', $salesm->igst, $partic, $date, $vtype);

            // Credit making charges
            if ($salesm->hmc > 0) $this->insertLine($slno, 'HMC', $salesm->hmc, $partic, $date, $vtype);

            // Credit TCS
            if ($salesm->tcs > 0) $this->insertLine($slno, 'TCS', $salesm->tcs, $partic, $date, $vtype);

            // Exchange (old gold) - debit exchange account
            if ($salesm->exchange_amount > 0) {
                $this->insertLine($slno, 'EP', -$salesm->exchange_amount, $partic, $date, $vtype);
            }

            // Sales return
            if (isset($salesm->sales_return) && $salesm->sales_return > 0) {
                $this->insertLine($slno, 'ESR', -$salesm->sales_return, $partic, $date, $vtype);
            }

            // Balancing round-off entry
            $sum = Daybook::where('slno', $slno)->sum('amount');
            if (abs($sum) > 0.0001) {
                $this->insertLine($slno, 'ROUND', -$sum, 'Round Off', $date, $vtype);
            }

            // Update/create daybookpart header
            Daybookpart::updateOrCreate(['slno' => $slno], [
                'vchno'      => $salesm->billno,
                'particular' => $partic,
                'tdate'      => $date,
                'vtype'      => $vtype,
                'narration'  => $salesm->narration,
                'created_by' => $salesm->created_by,
            ]);
        });
    }

    /**
     * Purchase Bill Daybook Entries
     * Rule: negative = Debit, positive = Credit
     *
     * EP (Purchase) -gross_amount   (Debit purchase account)
     * SGST/CGST/IGST -tax          (Debit input tax)
     * HMC           -hmc            (Debit making charge)
     * TCS           -tcs            (Debit TCS)
     * DISC          +discount       (Credit discount received)
     * Supplier      +net_amount     (Credit supplier - we owe them)
     * Supplier      -paid_amount    (Debit supplier - we pay them)
     * Cash/Bank     +paid_amount    (Credit cash/bank - we paid)
     * EP (Exch)     +exchange_amount (Credit exchange if old gold given)
     * ROUND         balancing
     */
    public function insertPurchaseDaybookEntries($purchasem): void
    {
        DB::transaction(function () use ($purchasem) {
            $slno   = $purchasem->slno;
            $date   = $purchasem->billdate;
            $vtype  = 'PURCHASE';
            $partic = 'Purchase Bill: ' . $purchasem->docno . ' - ' . $purchasem->supplier_name;

            $this->clearEntries($slno);

            $cashBankCode = 'CASH';
            if (in_array($purchasem->payment_mode, ['bank'])) {
                $bankAcc = Account::find($purchasem->bank_account_id);
                $cashBankCode = $bankAcc ? $bankAcc->code : 'BANK';
            }

            // Debit purchase account (EP)
            $this->insertLine($slno, 'EP', -$purchasem->gross_amount, $partic, $date, $vtype);

            // Debit input GST
            if ($purchasem->sgst > 0) $this->insertLine($slno, 'SGST', -$purchasem->sgst, $partic, $date, $vtype);
            if ($purchasem->cgst > 0) $this->insertLine($slno, 'CGST', -$purchasem->cgst, $partic, $date, $vtype);
            if ($purchasem->igst > 0) $this->insertLine($slno, 'IGST', -$purchasem->igst, $partic, $date, $vtype);

            // Debit making charges
            if ($purchasem->hmc > 0) $this->insertLine($slno, 'HMC', -$purchasem->hmc, $partic, $date, $vtype);

            // Debit TCS
            if ($purchasem->tcs > 0) $this->insertLine($slno, 'TCS', -$purchasem->tcs, $partic, $date, $vtype);

            // Credit discount received
            if ($purchasem->discount > 0) $this->insertLine($slno, 'DISC', $purchasem->discount, $partic, $date, $vtype);

            // Exchange given (credit exchange - old gold returned to supplier)
            if ($purchasem->exchange_amount > 0) {
                $this->insertLine($slno, 'EP', $purchasem->exchange_amount, 'Exchange: ' . $partic, $date, $vtype);
            }

            // Credit supplier for full net amount
            $supplierCode = $purchasem->supplier_id ? (Account::find($purchasem->supplier_id)?->code ?? 'SUPP') : 'SUPP';
            $this->insertLine($slno, $supplierCode, $purchasem->net_amount, $partic, $date, $vtype);

            // If paid, debit supplier and credit cash/bank
            if ($purchasem->paid_amount > 0) {
                $this->insertLine($slno, $supplierCode, -$purchasem->paid_amount, $partic, $date, $vtype);
                $this->insertLine($slno, $cashBankCode, $purchasem->paid_amount, $partic, $date, $vtype);
            }

            // Balancing round-off
            $sum = Daybook::where('slno', $slno)->sum('amount');
            if (abs($sum) > 0.0001) {
                $this->insertLine($slno, 'ROUND', -$sum, 'Round Off', $date, $vtype);
            }

            Daybookpart::updateOrCreate(['slno' => $slno], [
                'vchno'      => $purchasem->docno,
                'particular' => $partic,
                'tdate'      => $date,
                'vtype'      => $vtype,
                'narration'  => $purchasem->narration,
                'created_by' => $purchasem->created_by,
            ]);
        });
    }

    /**
     * Receipt Voucher Daybook Entries
     * Cash/Bank   -amount           (Debit - we receive cash)
     * DISC        -discount         (Debit discount allowed)
     * Party       +(amount+discount) (Credit party - reduces receivable)
     */
    public function insertReceiptDaybookEntries($receipt): void
    {
        DB::transaction(function () use ($receipt) {
            $slno   = $receipt->slno;
            $date   = $receipt->vchdate;
            $vtype  = 'RECEIPT';
            $partic = 'Receipt: ' . $receipt->vchno . ' - ' . $receipt->party_name;

            $this->clearEntries($slno);

            $cashBankCode = 'CASH';
            if (in_array($receipt->payment_mode, ['bank','cheque','pdc'])) {
                if ($receipt->bank_account_id) {
                    $bankAcc = Account::find($receipt->bank_account_id);
                    $cashBankCode = $bankAcc ? $bankAcc->code : 'BANK';
                } else {
                    $cashBankCode = 'BANK';
                }
            }

            // Debit cash/bank (we receive money)
            $this->insertLine($slno, $cashBankCode, -$receipt->amount, $partic, $date, $vtype);

            // Debit discount allowed
            if ($receipt->discount > 0) {
                $this->insertLine($slno, 'DISC', -$receipt->discount, $partic, $date, $vtype);
            }

            // Credit party (reduces their balance we're owed)
            $partyCode = $receipt->party_id ? (Account::find($receipt->party_id)?->code ?? 'PARTY') : 'PARTY';
            $this->insertLine($slno, $partyCode, $receipt->amount + $receipt->discount, $partic, $date, $vtype);

            // Balancing
            $sum = Daybook::where('slno', $slno)->sum('amount');
            if (abs($sum) > 0.0001) {
                $this->insertLine($slno, 'ROUND', -$sum, 'Round Off', $date, $vtype);
            }

            Daybookpart::updateOrCreate(['slno' => $slno], [
                'vchno'      => $receipt->vchno,
                'particular' => $partic,
                'tdate'      => $date,
                'vtype'      => $vtype,
                'narration'  => $receipt->narration,
                'created_by' => $receipt->created_by,
            ]);
        });
    }

    /**
     * Payment Voucher Daybook Entries
     * Cash/Bank   +amount    (Credit cash/bank - we pay out)
     * Party       -amount    (Debit party - reduces payable)
     */
    public function insertPaymentDaybookEntries($payment): void
    {
        DB::transaction(function () use ($payment) {
            $slno   = $payment->slno;
            $date   = $payment->vchdate;
            $vtype  = 'PAYMENT';
            $partic = 'Payment: ' . $payment->vchno . ' - ' . $payment->party_name;

            $this->clearEntries($slno);

            $cashBankCode = 'CASH';
            if (in_array($payment->payment_mode, ['bank','cheque','pdc'])) {
                if ($payment->bank_account_id) {
                    $bankAcc = Account::find($payment->bank_account_id);
                    $cashBankCode = $bankAcc ? $bankAcc->code : 'BANK';
                } else {
                    $cashBankCode = 'BANK';
                }
            }

            // Credit cash/bank (we pay out)
            $this->insertLine($slno, $cashBankCode, $payment->amount, $partic, $date, $vtype);

            // Debit party (reduces what we owe them)
            $partyCode = $payment->party_id ? (Account::find($payment->party_id)?->code ?? 'PARTY') : 'PARTY';
            $this->insertLine($slno, $partyCode, -$payment->amount, $partic, $date, $vtype);

            // Balancing
            $sum = Daybook::where('slno', $slno)->sum('amount');
            if (abs($sum) > 0.0001) {
                $this->insertLine($slno, 'ROUND', -$sum, 'Round Off', $date, $vtype);
            }

            Daybookpart::updateOrCreate(['slno' => $slno], [
                'vchno'      => $payment->vchno,
                'particular' => $partic,
                'tdate'      => $date,
                'vtype'      => $vtype,
                'narration'  => $payment->narration,
                'created_by' => $payment->created_by,
            ]);
        });
    }

    /**
     * Validate that a transaction balances (SUM = 0)
     */
    public function validateBalance(string $slno): bool
    {
        $sum = Daybook::where('slno', $slno)->sum('amount');
        return abs((float)$sum) <= 0.0001;
    }

    /**
     * Generate next sequence number
     * Format: PREFIX/YYMM/0001
     */
    public function nextSlno(string $prefix, string $table, string $column = 'slno'): string
    {
        $fy = $this->getFySuffix();
        $like = $prefix . '/' . $fy . '/%';
        $last = DB::table($table)->where($column, 'like', $like)->orderBy($column, 'desc')->value($column);
        if ($last) {
            $parts = explode('/', $last);
            $num = (int)end($parts) + 1;
        } else {
            $num = 1;
        }
        return $prefix . '/' . $fy . '/' . str_pad($num, 4, '0', STR_PAD_LEFT);
    }

    protected function getFySuffix(): string
    {
        $month = (int)date('m');
        $year  = (int)date('Y');
        if ($month >= 4) {
            return substr($year, 2) . substr($year + 1, 2);
        } else {
            return substr($year - 1, 2) . substr($year, 2);
        }
    }
}
