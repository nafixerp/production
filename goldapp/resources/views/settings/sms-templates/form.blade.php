@extends('layouts.app')
@section('title', $template->exists ? 'Edit SMS/WA Template' : 'New SMS/WA Template')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-chat-dots me-2"></i>{{ $template->exists ? 'Edit — '.$template->name : 'New Template' }}</h5>
    <a href="{{ route('sms-templates.index') }}" class="btn-outline-gold btn-sm-gold"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>
<div class="card-gold" style="max-width:640px">
    <form method="POST" action="{{ $template->exists ? route('sms-templates.update', $template) : route('sms-templates.store') }}">
        @csrf
        @if($template->exists) @method('PUT') @endif

        <div class="row g-3 mb-3">
            <div class="col-6">
                <label class="form-label">Name *</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $template->name) }}" required>
            </div>
            <div class="col-6">
                <label class="form-label">Channel *</label>
                <select name="channel" class="form-select">
                    <option value="sms" {{ old('channel', $template->channel) === 'sms' ? 'selected' : '' }}>SMS</option>
                    <option value="whatsapp" {{ old('channel', $template->channel) === 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                </select>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-6">
                <label class="form-label">Module *</label>
                <select name="module" class="form-select">
                    @foreach(['sales','purchase','production','inventory','accounts','hr','dispatch','crm','general'] as $mod)
                        <option value="{{ $mod }}" {{ old('module', $template->module) === $mod ? 'selected' : '' }}>{{ ucfirst($mod) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6">
                <label class="form-label">DLT Template ID</label>
                <input type="text" name="template_id" class="form-control" value="{{ old('template_id', $template->template_id) }}" placeholder="For regulatory compliance">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Message *</label>
            <textarea name="message" class="form-control" rows="5" required>{{ old('message', $template->message) }}</textarea>
            <small style="color:var(--text-muted-gold)">Use {variable_name} for dynamic content. Max 160 chars for SMS.</small>
        </div>

        <div class="mb-3">
            <label class="form-label">Variables</label>
            <input type="text" name="variables_list" class="form-control" value="{{ old('variables_list', $template->variables_list) }}" placeholder="customer_name, order_no, amount">
        </div>

        <div class="mb-4">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive"
                    {{ old('is_active', $template->is_active ?? 1) ? 'checked' : '' }}>
                <label class="form-check-label" for="isActive" style="color:#ccc">Active</label>
            </div>
        </div>

        <button type="submit" class="btn-gold"><i class="bi bi-floppy me-1"></i>{{ $template->exists ? 'Update' : 'Create' }} Template</button>
    </form>
</div>
@endsection
