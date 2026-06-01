<?php
namespace App\Http\Controllers;

use App\Models\CrmCustomer;
use App\Models\CrmActivity;
use App\Models\CustomerComplaint;
use App\Models\Daybook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $q = CrmCustomer::query();
        if ($request->search) {
            $s = $request->search;
            $q->where(function($query) use ($s) {
                $query->where('name','like',"%$s%")
                      ->orWhere('code','like',"%$s%")
                      ->orWhere('phone','like',"%$s%")
                      ->orWhere('email','like',"%$s%");
            });
        }
        if ($request->customer_type) $q->where('customer_type', $request->customer_type);
        if ($request->status !== null && $request->status !== '') $q->where('status', $request->status);
        $customers = $q->orderBy('name')->paginate(25)->withQueryString();
        return view('crm.customers.index', compact('customers'));
    }

    public function create()
    {
        $nextCode = 'CUST-' . str_pad(CrmCustomer::max('id') + 1, 5, '0', STR_PAD_LEFT);
        return view('crm.customers.form', compact('nextCode'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code'            => 'required|string|max:30|unique:customers,code',
            'name'            => 'required|string|max:180',
            'contact_person'  => 'nullable|string|max:100',
            'phone'           => 'required|string|max:20',
            'alt_phone'       => 'nullable|string|max:20',
            'email'           => 'nullable|email|max:180',
            'address'         => 'nullable|string',
            'city'            => 'nullable|string|max:80',
            'state'           => 'nullable|string|max:80',
            'pincode'         => 'nullable|string|max:10',
            'country'         => 'nullable|string|max:50',
            'gst_no'          => 'nullable|string|max:30',
            'pan_no'          => 'nullable|string|max:20',
            'customer_type'   => 'required|in:retail,wholesale,distributor,online,export',
            'credit_limit'    => 'nullable|numeric|min:0',
            'credit_days'     => 'nullable|integer|min:0',
            'payment_terms'   => 'nullable|string|max:100',
            'price_tier'      => 'required|in:standard,tier1,tier2,tier3',
            'dob'             => 'nullable|date',
            'anniversary'     => 'nullable|date',
            'notes'           => 'nullable|string',
            'status'          => 'nullable|integer',
        ]);
        $data['created_by'] = auth()->id();
        $data['status'] = $request->status ?? 1;
        CrmCustomer::create($data);
        return redirect()->route('customers.index')->with('success', 'Customer created successfully.');
    }

    public function show($id)
    {
        $customer = CrmCustomer::findOrFail($id);
        $activities = CrmActivity::where('customer_id', $id)->orderBy('activity_date','desc')->limit(20)->get();
        $complaints = CustomerComplaint::where('customer_id', $id)->orderBy('complaint_date','desc')->limit(10)->get();
        // Daybook transactions for this customer (if linked via account)
        $transactions = Daybook::where('account_name', 'like', "%{$customer->name}%")
            ->orWhere('particular', 'like', "%{$customer->code}%")
            ->orderBy('tdate','desc')->limit(30)->get();
        // Check credit limit
        $creditWarning = $customer->credit_limit > 0 && $customer->outstanding_balance >= $customer->credit_limit;
        return view('crm.customers.show', compact('customer','activities','complaints','transactions','creditWarning'));
    }

    public function edit($id)
    {
        $customer = CrmCustomer::findOrFail($id);
        return view('crm.customers.form', compact('customer'));
    }

    public function update(Request $request, $id)
    {
        $customer = CrmCustomer::findOrFail($id);
        $data = $request->validate([
            'code'            => "required|string|max:30|unique:customers,code,{$id}",
            'name'            => 'required|string|max:180',
            'contact_person'  => 'nullable|string|max:100',
            'phone'           => 'required|string|max:20',
            'alt_phone'       => 'nullable|string|max:20',
            'email'           => 'nullable|email|max:180',
            'address'         => 'nullable|string',
            'city'            => 'nullable|string|max:80',
            'state'           => 'nullable|string|max:80',
            'pincode'         => 'nullable|string|max:10',
            'country'         => 'nullable|string|max:50',
            'gst_no'          => 'nullable|string|max:30',
            'pan_no'          => 'nullable|string|max:20',
            'customer_type'   => 'required|in:retail,wholesale,distributor,online,export',
            'credit_limit'    => 'nullable|numeric|min:0',
            'credit_days'     => 'nullable|integer|min:0',
            'payment_terms'   => 'nullable|string|max:100',
            'price_tier'      => 'required|in:standard,tier1,tier2,tier3',
            'dob'             => 'nullable|date',
            'anniversary'     => 'nullable|date',
            'notes'           => 'nullable|string',
            'status'          => 'nullable|integer',
        ]);
        $data['status'] = $request->status ?? 1;
        $customer->update($data);
        return redirect()->route('customers.index')->with('success', 'Customer updated successfully.');
    }

    public function destroy($id)
    {
        $customer = CrmCustomer::findOrFail($id);
        if ($customer->outstanding_balance != 0) {
            return back()->with('error', 'Cannot delete customer with outstanding balance.');
        }
        $customer->delete();
        return redirect()->route('customers.index')->with('success', 'Customer deleted.');
    }
}
