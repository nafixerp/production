<?php

namespace App\Http\Controllers;

use App\Models\FinancialYear;
use Illuminate\Http\Request;

class FinancialYearController extends Controller
{
    public function index()
    {
        $years = FinancialYear::orderByDesc('from_date')->get();
        return view('settings.financial-years.index', compact('years'));
    }

    public function create()
    {
        return view('settings.financial-years.form', ['year' => new FinancialYear()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:20|unique:financial_years,name',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after:from_date',
        ]);

        FinancialYear::create(array_merge($data, ['is_current' => 0, 'is_locked' => 0]));
        return redirect()->route('financial-years.index')->with('success', 'Financial year created.');
    }

    public function show(FinancialYear $financialYear)
    {
        return view('settings.financial-years.form', ['year' => $financialYear]);
    }

    public function edit(FinancialYear $financialYear)
    {
        return view('settings.financial-years.form', ['year' => $financialYear]);
    }

    public function update(Request $request, FinancialYear $financialYear)
    {
        if ($financialYear->is_locked) {
            return redirect()->back()->with('error', 'Locked financial year cannot be edited.');
        }
        $data = $request->validate([
            'name' => 'required|string|max:20|unique:financial_years,name,' . $financialYear->id,
            'from_date' => 'required|date',
            'to_date' => 'required|date|after:from_date',
        ]);
        $financialYear->update($data);
        return redirect()->route('financial-years.index')->with('success', 'Financial year updated.');
    }

    public function destroy(FinancialYear $financialYear)
    {
        if ($financialYear->is_current || $financialYear->is_locked) {
            return redirect()->back()->with('error', 'Cannot delete current or locked financial year.');
        }
        $financialYear->delete();
        return redirect()->route('financial-years.index')->with('success', 'Financial year deleted.');
    }

    public function lock(int $id)
    {
        $year = FinancialYear::findOrFail($id);
        $year->update([
            'is_locked' => !$year->is_locked,
            'locked_by' => $year->is_locked ? null : auth()->id(),
            'locked_at' => $year->is_locked ? null : now(),
        ]);
        $msg = $year->is_locked ? 'Financial year locked.' : 'Financial year unlocked.';
        return redirect()->route('financial-years.index')->with('success', $msg);
    }

    public function setCurrent(int $id)
    {
        FinancialYear::query()->update(['is_current' => 0]);
        FinancialYear::findOrFail($id)->update(['is_current' => 1]);
        return redirect()->route('financial-years.index')->with('success', 'Current financial year set.');
    }
}
