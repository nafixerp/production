<?php

namespace App\Http\Controllers;

use App\Models\Daybook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalaryPostingController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->get('month');
        $year  = $request->get('year', now()->year);

        // Fetch daybook entries with slno starting with SAL/
        $entries = DB::table('daybook_parts')
            ->where('slno', 'like', 'SAL/%')
            ->when($month, function ($q) use ($month, $year) {
                $ym = sprintf('%02d%02d', substr($year, 2), $month);
                $q->where('slno', 'like', "SAL/{$ym}/%");
            })
            ->orderByDesc('tdate')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        return view('hrms.salary-posting.index', compact('entries', 'month', 'year'));
    }

    public function create()
    {
        return redirect()->route('salary-posting.index');
    }

    public function store(Request $request)
    {
        return redirect()->route('salary-posting.index');
    }

    public function show($id)
    {
        // Show all daybook lines for this slno
        $part  = DB::table('daybook_parts')->where('id', $id)->first();
        $lines = $part ? DB::table('daybook')->where('slno', $part->slno)->get() : collect();
        return view('hrms.salary-posting.show', compact('part', 'lines'));
    }

    public function edit($id)
    {
        return $this->show($id);
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('salary-posting.index');
    }

    public function destroy($id)
    {
        return redirect()->route('salary-posting.index');
    }
}
