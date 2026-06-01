<?php
namespace App\Http\Controllers;

use App\Models\Daybook;
use App\Models\Daybookpart;
use Illuminate\Http\Request;

class DaybookController extends Controller
{
    public function index(Request $request)
    {
        $from  = $request->from ?? date('Y-m-d');
        $to    = $request->to   ?? date('Y-m-d');
        $vtype = $request->vtype;

        $entries = Daybook::with('account')
            ->whereBetween('tdate', [$from, $to])
            ->when($vtype, fn($q) => $q->where('vtype', $vtype))
            ->orderBy('tdate')->orderBy('slno')
            ->get();

        // Group by slno
        $grouped = $entries->groupBy('slno');

        return view('daybook.index', compact('grouped','from','to','vtype'));
    }
}
