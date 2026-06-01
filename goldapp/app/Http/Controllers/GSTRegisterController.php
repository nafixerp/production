<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GSTRegisterController extends Controller
{
    public function index(Request $request)
    {
        return redirect()->route('gstr1.index');
    }

    public function gstr1(Request $request)
    {
        $fromDate = $request->from ?? date('Y-m-01');
        $toDate   = $request->to ?? date('Y-m-t');

        // Sales invoices with GST details
        $salesData = DB::table('salesm')
            ->leftJoin('salesd','salesm.id','=','salesd.sales_id')
            ->leftJoin('customers as c','salesm.customer_id','=','c.id')
            ->whereBetween('salesm.invoice_date', [$fromDate, $toDate])
            ->selectRaw("
                salesm.id, salesm.invoice_no, salesm.invoice_date,
                c.name as customer_name, c.gst_no as customer_gstin, c.state as customer_state,
                SUM(salesd.taxable_amount) as taxable_value,
                SUM(salesd.cgst_amount) as cgst,
                SUM(salesd.sgst_amount) as sgst,
                SUM(salesd.igst_amount) as igst,
                SUM(salesd.total_amount) as invoice_value
            ")
            ->groupBy('salesm.id','salesm.invoice_no','salesm.invoice_date',
                      'c.name','c.gst_no','c.state')
            ->orderBy('salesm.invoice_date','asc')
            ->get();

        $totalTaxable = $salesData->sum('taxable_value');
        $totalCGST    = $salesData->sum('cgst');
        $totalSGST    = $salesData->sum('sgst');
        $totalIGST    = $salesData->sum('igst');
        $totalInvoice = $salesData->sum('invoice_value');

        return view('finance.gst-register.gstr1', compact(
            'salesData','totalTaxable','totalCGST','totalSGST','totalIGST','totalInvoice','fromDate','toDate'
        ));
    }

    public function gstr3b(Request $request)
    {
        $fromDate = $request->from ?? date('Y-m-01');
        $toDate   = $request->to ?? date('Y-m-t');

        // Outward supplies (sales)
        $outward = DB::table('salesm')
            ->leftJoin('salesd','salesm.id','=','salesd.sales_id')
            ->whereBetween('salesm.invoice_date', [$fromDate, $toDate])
            ->selectRaw("
                SUM(salesd.taxable_amount) as taxable,
                SUM(salesd.cgst_amount) as cgst,
                SUM(salesd.sgst_amount) as sgst,
                SUM(salesd.igst_amount) as igst,
                SUM(salesd.cess_amount) as cess
            ")->first();

        // Inward supplies (purchases eligible for ITC)
        $inward = DB::table('purchasem')
            ->leftJoin('purchasedd','purchasem.id','=','purchasedd.purchase_id')
            ->whereBetween('purchasem.bill_date', [$fromDate, $toDate])
            ->selectRaw("
                SUM(purchasedd.taxable_amount) as taxable,
                SUM(purchasedd.cgst_amount) as cgst,
                SUM(purchasedd.sgst_amount) as sgst,
                SUM(purchasedd.igst_amount) as igst,
                SUM(purchasedd.cess_amount) as cess
            ")->first();

        $netTaxPayable = [
            'cgst' => ($outward->cgst ?? 0) - ($inward->cgst ?? 0),
            'sgst' => ($outward->sgst ?? 0) - ($inward->sgst ?? 0),
            'igst' => ($outward->igst ?? 0) - ($inward->igst ?? 0),
            'cess' => ($outward->cess ?? 0) - ($inward->cess ?? 0),
        ];

        return view('finance.gst-register.gstr3b', compact(
            'outward','inward','netTaxPayable','fromDate','toDate'
        ));
    }

    // Purchase GST register
    public function purchase(Request $request)
    {
        $fromDate = $request->from ?? date('Y-m-01');
        $toDate   = $request->to ?? date('Y-m-t');

        $purchaseData = DB::table('purchasem')
            ->leftJoin('purchasedd','purchasem.id','=','purchasedd.purchase_id')
            ->leftJoin('vendor_suppliers as v','purchasem.supplier_id','=','v.id')
            ->whereBetween('purchasem.bill_date', [$fromDate, $toDate])
            ->selectRaw("
                purchasem.id, purchasem.bill_no, purchasem.bill_date,
                v.name as supplier_name, v.gst_no as supplier_gstin,
                SUM(purchasedd.taxable_amount) as taxable_value,
                SUM(purchasedd.cgst_amount) as cgst,
                SUM(purchasedd.sgst_amount) as sgst,
                SUM(purchasedd.igst_amount) as igst,
                SUM(purchasedd.total_amount) as invoice_value
            ")
            ->groupBy('purchasem.id','purchasem.bill_no','purchasem.bill_date',
                      'v.name','v.gst_no')
            ->orderBy('purchasem.bill_date','asc')
            ->get();

        $totalTaxable = $purchaseData->sum('taxable_value');
        $totalCGST    = $purchaseData->sum('cgst');
        $totalSGST    = $purchaseData->sum('sgst');
        $totalIGST    = $purchaseData->sum('igst');

        return view('finance.gst-register.purchase', compact(
            'purchaseData','totalTaxable','totalCGST','totalSGST','totalIGST','fromDate','toDate'
        ));
    }
}
