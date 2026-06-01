<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use Illuminate\Http\Request;

class ShiftManagementController extends Controller
{
    public function index(Request $request)
    {
        $q      = $request->get('q');
        $shifts = Shift::when($q, fn($query) => $query->where('name', 'like', "%$q%")->orWhere('code', 'like', "%$q%"))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('hrms.shifts.index', compact('shifts', 'q'));
    }

    public function create()
    {
        $shift = null;
        return view('hrms.shifts.form', compact('shift'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:100',
            'code'          => 'required|string|max:20|unique:shifts',
            'start_time'    => 'required',
            'end_time'      => 'required',
            'break_minutes' => 'nullable|integer|min:0',
            'working_hours' => 'nullable|numeric|min:0',
            'status'        => 'nullable|in:active,inactive',
        ]);

        Shift::create($data);

        return redirect()->route('shifts.index')->with('success', 'Shift created successfully.');
    }

    public function show($id)
    {
        $shift = Shift::findOrFail($id);
        return view('hrms.shifts.form', ['shift' => $shift]);
    }

    public function edit($id)
    {
        $shift = Shift::findOrFail($id);
        return view('hrms.shifts.form', compact('shift'));
    }

    public function update(Request $request, $id)
    {
        $shift = Shift::findOrFail($id);

        $data = $request->validate([
            'name'          => 'required|string|max:100',
            'code'          => 'required|string|max:20|unique:shifts,code,' . $id,
            'start_time'    => 'required',
            'end_time'      => 'required',
            'break_minutes' => 'nullable|integer|min:0',
            'working_hours' => 'nullable|numeric|min:0',
            'status'        => 'nullable|in:active,inactive',
        ]);

        $shift->update($data);

        return redirect()->route('shifts.index')->with('success', 'Shift updated successfully.');
    }

    public function destroy($id)
    {
        Shift::findOrFail($id)->delete();
        return redirect()->route('shifts.index')->with('success', 'Shift deleted.');
    }
}
