<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApprovalWorkflowController extends Controller
{
    protected $table = 'approval_workflows';
    protected $title = 'Approval Workflow';
    protected $slug  = 'approval-workflow';

    public function index(Request $request)
    {
        $q = $request->get('q');
        $rows = DB::table($this->table)
            ->when($q, fn($qb) => $qb->where('name','like',"%$q%"))
            ->orderByDesc('id')->paginate(20)->withQueryString();
        return view('generic.index', ['rows'=>$rows,'title'=>$this->title,'slug'=>$this->slug,'q'=>$q]);
    }
    public function create() { return view('generic.create', ['row'=>null,'title'=>$this->title,'slug'=>$this->slug]); }
    public function store(Request $request) {
        DB::table($this->table)->insert(['name'=>$request->name,'code'=>$request->code,'status'=>$request->status??'active','description'=>$request->description,'created_at'=>now(),'updated_at'=>now()]);
        return redirect()->route($this->slug.'.index')->with('success','Saved');
    }
    public function show($id) {
        $row = DB::table($this->table)->find($id);
        return view('generic.show', ['row'=>$row,'title'=>$this->title,'slug'=>$this->slug]);
    }
    public function edit($id) {
        $row = DB::table($this->table)->find($id);
        return view('generic.create', ['row'=>$row,'title'=>$this->title,'slug'=>$this->slug]);
    }
    public function update(Request $request, $id) {
        DB::table($this->table)->where('id',$id)->update(['name'=>$request->name,'code'=>$request->code,'status'=>$request->status??'active','description'=>$request->description,'updated_at'=>now()]);
        return redirect()->route($this->slug.'.index')->with('success','Updated');
    }
    public function destroy($id) {
        DB::table($this->table)->delete($id);
        return redirect()->route($this->slug.'.index')->with('success','Deleted');
    }
}
