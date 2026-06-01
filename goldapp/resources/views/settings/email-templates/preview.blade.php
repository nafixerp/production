@extends('layouts.app')
@section('title','Preview — ' . $emailTemplate->name)
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-envelope-open me-2"></i>Preview — {{ $emailTemplate->name }}</h5>
    <div class="d-flex gap-2">
        <a href="{{ route('email-templates.edit', $emailTemplate) }}" class="btn-outline-gold btn-sm-gold"><i class="bi bi-pencil me-1"></i>Edit</a>
        <a href="{{ route('email-templates.index') }}" class="btn-outline-gold btn-sm-gold"><i class="bi bi-arrow-left me-1"></i>Back</a>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-8">
        <div class="card-gold">
            <div class="card-header-gold"><h5>Email Preview</h5></div>
            <div style="margin-bottom:8px">
                <label class="form-label">Subject</label>
                <div style="background:rgba(255,255,255,.04);border:1px solid var(--border-gold);border-radius:5px;padding:8px 12px;color:#e0d8b8">
                    {{ $emailTemplate->subject }}
                </div>
            </div>
            <div>
                <label class="form-label">Body Preview</label>
                <div style="background:#fff;border-radius:6px;padding:20px;color:#333;min-height:200px">
                    {!! $previewBody ?? $emailTemplate->body !!}
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card-gold">
            <div class="card-header-gold"><h5>Template Info</h5></div>
            <table style="width:100%;font-size:.82rem">
                <tr><td style="color:var(--text-muted-gold);padding:4px 0">Module</td><td><span class="badge-gold">{{ $emailTemplate->module }}</span></td></tr>
                <tr><td style="color:var(--text-muted-gold);padding:4px 0">Status</td>
                    <td><span style="color:{{ $emailTemplate->is_active ? '#4ade80' : '#f87171' }}">{{ $emailTemplate->is_active ? 'Active' : 'Inactive' }}</span></td></tr>
            </table>
            @if($emailTemplate->variables_list)
            <div class="mt-3">
                <label class="form-label">Available Variables</label>
                @foreach(explode(',', $emailTemplate->variables_list) as $var)
                    <code class="badge-gold d-inline-block mb-1">{!! '{'.trim($var).'}' !!}</code>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
