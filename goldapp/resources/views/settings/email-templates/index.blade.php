@extends('layouts.app')
@section('title','Email Templates')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-envelope me-2"></i>Email Templates</h5>
    <a href="{{ route('email-templates.create') }}" class="btn-gold btn-sm-gold"><i class="bi bi-plus-circle me-1"></i>New Template</a>
</div>
<div class="card-gold">
    <table class="table-gold w-100">
        <thead>
            <tr><th>#</th><th>Name</th><th>Module</th><th>Subject</th><th>Status</th><th>Actions</th></tr>
        </thead>
        <tbody>
            @forelse($templates as $tpl)
            <tr>
                <td>{{ $tpl->id }}</td>
                <td style="font-weight:600">{{ $tpl->name }}</td>
                <td><span class="badge-gold">{{ $tpl->module }}</span></td>
                <td style="font-size:.8rem">{{ Str::limit($tpl->subject, 60) }}</td>
                <td>
                    @if($tpl->is_active)
                        <span style="color:#4ade80">Active</span>
                    @else
                        <span style="color:#f87171">Inactive</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('email-templates.show', $tpl) }}" class="btn-outline-gold btn-sm-gold me-1" title="Preview"><i class="bi bi-eye"></i></a>
                    <a href="{{ route('email-templates.edit', $tpl) }}" class="btn-outline-gold btn-sm-gold me-1"><i class="bi bi-pencil"></i></a>
                    <form method="POST" action="{{ route('email-templates.destroy', $tpl) }}" class="d-inline" onsubmit="return confirm('Delete?')">
                        @csrf @method('DELETE')
                        <button class="btn-outline-gold btn-sm-gold" style="color:#f87171;border-color:#f87171"><i class="bi bi-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center" style="color:#666;padding:30px">No email templates yet.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-3">{{ $templates->links() }}</div>
</div>
@endsection
