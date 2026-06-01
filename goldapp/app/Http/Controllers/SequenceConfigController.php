<?php

namespace App\Http\Controllers;

use App\Models\SequenceConfig;
use Illuminate\Http\Request;

class SequenceConfigController extends Controller
{
    public function index()
    {
        $sequences = SequenceConfig::orderBy('module')->get();
        return view('settings.sequences.index', compact('sequences'));
    }

    public function create()
    {
        return view('settings.sequences.form', ['seq' => new SequenceConfig()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'module' => 'required|string|max:60|unique:sequence_configs,module',
            'prefix' => 'required|string|max:20',
            'suffix' => 'nullable|string|max:20',
            'reset_cycle' => 'required|in:never,yearly,monthly,daily',
            'format' => 'required|string|max:50',
            'branch_specific' => 'nullable|boolean',
        ]);
        SequenceConfig::create(array_merge($data, ['current_seq' => 0]));
        return redirect()->route('sequences.index')->with('success', 'Sequence config created.');
    }

    public function show(SequenceConfig $sequence)
    {
        return view('settings.sequences.form', ['seq' => $sequence]);
    }

    public function edit(SequenceConfig $sequence)
    {
        return view('settings.sequences.form', ['seq' => $sequence]);
    }

    public function update(Request $request, SequenceConfig $sequence)
    {
        $data = $request->validate([
            'prefix' => 'required|string|max:20',
            'suffix' => 'nullable|string|max:20',
            'reset_cycle' => 'required|in:never,yearly,monthly,daily',
            'format' => 'required|string|max:50',
            'branch_specific' => 'nullable|boolean',
        ]);
        $sequence->update($data);
        return redirect()->route('sequences.index')->with('success', 'Sequence updated.');
    }

    public function destroy(SequenceConfig $sequence)
    {
        $sequence->delete();
        return redirect()->route('sequences.index')->with('success', 'Sequence deleted.');
    }

    public function preview(SequenceConfig $sequence)
    {
        return response()->json(['preview' => $sequence->previewNext()]);
    }

    public function reset(SequenceConfig $sequence)
    {
        $sequence->update(['current_seq' => 0]);
        return redirect()->route('sequences.index')->with('success', "Sequence for {$sequence->module} reset to 0.");
    }
}
