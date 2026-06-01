<?php
namespace App\Http\Controllers;

use App\Models\CustomerPricing;
use App\Models\CrmCustomer;
use App\Models\FinishedGoods;
use Illuminate\Http\Request;

class CustomerPricingController extends Controller
{
    public function index(Request $request)
    {
        $q = CustomerPricing::with(['customer','finishedGood']);
        if ($request->customer_id) $q->where('customer_id', $request->customer_id);
        if ($request->status !== null && $request->status !== '') $q->where('status', $request->status);
        $pricing = $q->orderBy('customer_id')->paginate(30)->withQueryString();
        $customers = CrmCustomer::where('status',1)->orderBy('name')->get();
        return view('crm.pricing.index', compact('pricing','customers'));
    }

    public function create()
    {
        $customers = CrmCustomer::where('status',1)->orderBy('name')->get();
        $products = FinishedGoods::where('status',1)->orderBy('name')->get();
        return view('crm.pricing.form', compact('customers','products'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id'  => 'required|integer',
            'fg_id'        => 'required|integer',
            'price_type'   => 'required|in:fixed,discount_pct',
            'price'        => 'nullable|numeric|min:0',
            'discount_pct' => 'nullable|numeric|min:0|max:100',
            'min_qty'      => 'nullable|numeric|min:0',
            'valid_from'   => 'required|date',
            'valid_to'     => 'nullable|date|after_or_equal:valid_from',
            'status'       => 'nullable|integer',
        ]);
        $data['status'] = $request->status ?? 1;
        CustomerPricing::create($data);
        return redirect()->route('customer-pricing.index')->with('success', 'Pricing rule created.');
    }

    public function show($id)
    {
        $pricing = CustomerPricing::with(['customer','finishedGood'])->findOrFail($id);
        return view('crm.pricing.show', compact('pricing'));
    }

    public function edit($id)
    {
        $pricing = CustomerPricing::findOrFail($id);
        $customers = CrmCustomer::where('status',1)->orderBy('name')->get();
        $products = FinishedGoods::where('status',1)->orderBy('name')->get();
        return view('crm.pricing.form', compact('pricing','customers','products'));
    }

    public function update(Request $request, $id)
    {
        $pricing = CustomerPricing::findOrFail($id);
        $data = $request->validate([
            'customer_id'  => 'required|integer',
            'fg_id'        => 'required|integer',
            'price_type'   => 'required|in:fixed,discount_pct',
            'price'        => 'nullable|numeric|min:0',
            'discount_pct' => 'nullable|numeric|min:0|max:100',
            'min_qty'      => 'nullable|numeric|min:0',
            'valid_from'   => 'required|date',
            'valid_to'     => 'nullable|date|after_or_equal:valid_from',
            'status'       => 'nullable|integer',
        ]);
        $data['status'] = $request->status ?? 1;
        $pricing->update($data);
        return redirect()->route('customer-pricing.index')->with('success', 'Pricing rule updated.');
    }

    public function destroy($id)
    {
        CustomerPricing::findOrFail($id)->delete();
        return redirect()->route('customer-pricing.index')->with('success', 'Pricing rule deleted.');
    }

    // Get effective price for a customer + item combination
    public function effectivePrice(Request $request)
    {
        $customerId = $request->customer_id;
        $fgId = $request->fg_id;
        $qty = $request->qty ?? 1;
        $today = today()->toDateString();

        $rule = CustomerPricing::where('customer_id', $customerId)
            ->where('fg_id', $fgId)
            ->where('status', 1)
            ->where('valid_from', '<=', $today)
            ->where(function($q) use ($today) {
                $q->whereNull('valid_to')->orWhere('valid_to', '>=', $today);
            })
            ->where('min_qty', '<=', $qty)
            ->orderBy('min_qty','desc')
            ->first();

        $fg = FinishedGoods::find($fgId);
        $basePrice = $fg ? $fg->selling_price ?? 0 : 0;

        if (!$rule) {
            return response()->json(['price' => $basePrice, 'rule' => null]);
        }

        $effectivePrice = $basePrice;
        if ($rule->price_type === 'fixed') {
            $effectivePrice = $rule->price;
        } elseif ($rule->price_type === 'discount_pct') {
            $effectivePrice = $basePrice * (1 - $rule->discount_pct / 100);
        }

        return response()->json(['price' => round($effectivePrice, 2), 'rule' => $rule]);
    }
}
