@extends('layouts.app')
@section('title','Sequence Config')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-123 me-2"></i>Sequence Configuration</h5>
    <a href="{{ route('sequences.create') }}" class="btn-gold btn-sm-gold"><i class="bi bi-plus-circle me-1"></i>Add Sequence</a>
</div>

<div class="card-gold">
    <table class="table-gold w-100">
        <thead>
            <tr>
                <th>Module</th><th>Prefix</th><th>Suffix</th><th>Format</th>
                <th>Current #</th><th>Next Preview</th><th>Reset Cycle</th><th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sequences as $seq)
            <tr>
                <td style="font-weight:600;color:var(--gold)">{{ $seq->module }}</td>
                <td><code class="text-gold">{{ $seq->prefix }}</code></td>
                <td>{{ $seq->suffix ?? '—' }}</td>
                <td style="font-size:.75rem;font-family:monospace">{{ $seq->format }}</td>
                <td>{{ $seq->current_seq }}</td>
                <td>
                    <code style="color:#60a5fa;font-size:.78rem" id="preview-{{ $seq->id }}">{{ $seq->previewNext() }}</code>
                </td>
                <td><span class="badge-gold">{{ $seq->reset_cycle }}</span></td>
                <td>
                    <a href="{{ route('sequences.edit', $seq) }}" class="btn-outline-gold btn-sm-gold me-1"><i class="bi bi-pencil"></i></a>
                    <form method="POST" action="/sequences/{{ $seq->id }}/reset" class="d-inline" onsubmit="return confirm('Reset sequence to 0?')">
                        @csrf
                        <button class="btn-outline-gold btn-sm-gold me-1" style="color:#fb923c;border-color:#fb923c" title="Reset">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </button>
                    </form>
                    <form method="POST" action="{{ route('sequences.destroy', $seq) }}" class="d-inline" onsubmit="return confirm('Delete?')">
                        @csrf @method('DELETE')
                        <button class="btn-outline-gold btn-sm-gold" style="color:#f87171;border-color:#f87171"><i class="bi bi-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center" style="color:#666;padding:30px">No sequences configured.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
