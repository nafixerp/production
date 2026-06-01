@extends('layouts.app')
@section('title','SMS / WhatsApp Templates')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-chat-dots me-2"></i>SMS / WhatsApp Templates</h5>
    <a href="{{ route('sms-templates.create') }}" class="btn-gold btn-sm-gold"><i class="bi bi-plus-circle me-1"></i>New Template</a>
</div>
<div class="card-gold">
    <table class="table-gold w-100">
        <thead>
            <tr><th>#</th><th>Name</th><th>Channel</th><th>Module</th><th>Template ID</th><th>Status</th><th>Actions</th></tr>
        </thead>
        <tbody>
            @forelse($templates as $tpl)
            <tr>
                <td>{{ $tpl->id }}</td>
                <td style="font-weight:600">{{ $tpl->name }}</td>
                <td>
                    @if($tpl->channel === 'whatsapp')
                        <span style="color:#25d366;font-weight:600"><i class="bi bi-whatsapp"></i> WhatsApp</span>
                    @else
                        <span style="color:#60a5fa;font-weight:600"><i class="bi bi-phone"></i> SMS</span>
                    @endif
                </td>
                <td><span class="badge-gold">{{ $tpl->module }}</span></td>
                <td style="font-size:.75rem;font-family:monospace">{{ $tpl->template_id ?? '—' }}</td>
                <td>
                    <span style="color:{{ $tpl->is_active ? '#4ade80' : '#f87171' }}">{{ $tpl->is_active ? 'Active' : 'Inactive' }}</span>
                </td>
                <td>
                    <a href="{{ route('sms-templates.edit', $tpl) }}" class="btn-outline-gold btn-sm-gold me-1"><i class="bi bi-pencil"></i></a>
                    <form method="POST" action="{{ route('sms-templates.destroy', $tpl) }}" class="d-inline" onsubmit="return confirm('Delete?')">
                        @csrf @method('DELETE')
                        <button class="btn-outline-gold btn-sm-gold" style="color:#f87171;border-color:#f87171"><i class="bi bi-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center" style="color:#666;padding:30px">No templates yet.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-3">{{ $templates->links() }}</div>
</div>
@endsection
