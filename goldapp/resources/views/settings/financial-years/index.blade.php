@extends('layouts.app')
@section('title','Financial Years')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-calendar3 me-2"></i>Financial Years</h5>
    <a href="{{ route('financial-years.create') }}" class="btn-gold btn-sm-gold"><i class="bi bi-plus-circle me-1"></i>Add Year</a>
</div>

<div class="card-gold">
    <table class="table-gold w-100">
        <thead>
            <tr><th>#</th><th>Name</th><th>From</th><th>To</th><th>Current</th><th>Locked</th><th>Locked By</th><th>Actions</th></tr>
        </thead>
        <tbody>
            @forelse($years as $year)
            <tr>
                <td>{{ $year->id }}</td>
                <td style="font-weight:600;color:var(--gold)">{{ $year->name }}</td>
                <td>{{ $year->from_date?->format('d/m/Y') }}</td>
                <td>{{ $year->to_date?->format('d/m/Y') }}</td>
                <td>
                    @if($year->is_current)
                        <span style="color:#4ade80;font-weight:700"><i class="bi bi-check-circle-fill"></i> Current</span>
                    @else
                        <form method="POST" action="{{ route('financial-years.set-current', $year->id) }}" class="d-inline">
                            @csrf
                            <button class="btn-outline-gold btn-sm-gold">Set Current</button>
                        </form>
                    @endif
                </td>
                <td>
                    @if($year->is_locked)
                        <span style="color:#f87171"><i class="bi bi-lock-fill"></i> Locked</span>
                    @else
                        <span style="color:#4ade80"><i class="bi bi-unlock-fill"></i> Open</span>
                    @endif
                </td>
                <td style="font-size:.75rem">{{ $year->locked_at?->format('d/m/Y') ?? '—' }}</td>
                <td>
                    <a href="{{ route('financial-years.edit', $year) }}" class="btn-outline-gold btn-sm-gold me-1"><i class="bi bi-pencil"></i></a>
                    <form method="POST" action="{{ route('financial-years.lock', $year->id) }}" class="d-inline">
                        @csrf
                        <button class="btn-outline-gold btn-sm-gold me-1" title="{{ $year->is_locked ? 'Unlock' : 'Lock' }}">
                            <i class="bi bi-{{ $year->is_locked ? 'unlock' : 'lock' }}"></i>
                        </button>
                    </form>
                    @if(!$year->is_current && !$year->is_locked)
                    <form method="POST" action="{{ route('financial-years.destroy', $year) }}" class="d-inline" onsubmit="return confirm('Delete?')">
                        @csrf @method('DELETE')
                        <button class="btn-outline-gold btn-sm-gold" style="color:#f87171;border-color:#f87171"><i class="bi bi-trash"></i></button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center" style="color:#666;padding:30px">No financial years defined.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
