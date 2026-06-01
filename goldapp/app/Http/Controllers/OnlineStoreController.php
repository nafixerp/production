<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OnlineStoreController extends Controller
{
    protected $table = 'ecom_order';
    protected $title = 'Online Store/Orders';

    public function index(Request $request)
    {
        $q = $request->get('q');
        $rows = DB::table($this->table)
            ->when($q, fn($query) => $query->where('name','like',"%$q%")->orWhere('code','like',"%$q%"))
            ->orderByDesc('id')->paginate(20)->withQueryString();
        return view('generic.index', ['rows'=>$rows,'title'=>$this->title,'slug'=>'ecom-orders','q'=>$q,'table'=>$this->table]);
    }

    public function create()
    {
        return view('generic.create', ['title'=>$this->title,'slug'=>'ecom-orders','table'=>$this->table,'row'=>null]);
    }

    public function store(Request $request)
    {
        $data = $request->except(['_token','_method']);
        $data['created_at'] = now();
        $data['updated_at'] = now();
        $data['status'] = $data['status'] ?? 'active';
        DB::table($this->table)->insert($data);
        return redirect()->route('ecom-orders.index')->with('success', '$this->title saved successfully.');
    }

    public function show($id)
    {
        $row = DB::table($this->table)->find($id);
        return view('generic.show', ['title'=>$this->title,'slug'=>'ecom-orders','table'=>$this->table,'row'=>$row]);
    }

    public function edit($id)
    {
        $row = DB::table($this->table)->find($id);
        return view('generic.create', ['title'=>$this->title,'slug'=>'ecom-orders','table'=>$this->table,'row'=>$row]);
    }

    public function update(Request $request, $id)
    {
        $data = $request->except(['_token','_method']);
        $data['updated_at'] = now();
        DB::table($this->table)->where('id',$id)->update($data);
        return redirect()->route('ecom-orders.index')->with('success', '$this->title updated successfully.');
    }

    public function destroy($id)
    {
        DB::table($this->table)->where('id',$id)->delete();
        return redirect()->route('ecom-orders.index')->with('success', '$this->title deleted.');
    }
}
