@extends('layouts.app')
@section('title', $template->exists ? 'Edit Email Template' : 'New Email Template')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-envelope me-2"></i>{{ $template->exists ? 'Edit — '.$template->name : 'New Email Template' }}</h5>
    <a href="{{ route('email-templates.index') }}" class="btn-outline-gold btn-sm-gold"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>
<div class="card-gold">
    <form method="POST" action="{{ $template->exists ? route('email-templates.update', $template) : route('email-templates.store') }}">
        @csrf
        @if($template->exists) @method('PUT') @endif

        <div class="row g-3 mb-3">
            <div class="col-6">
                <label class="form-label">Template Name *</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $template->name) }}" required>
            </div>
            <div class="col-6">
                <label class="form-label">Module *</label>
                <select name="module" class="form-select">
                    @foreach(['sales','purchase','production','inventory','accounts','hr','dispatch','crm','general'] as $mod)
                        <option value="{{ $mod }}" {{ old('module', $template->module) === $mod ? 'selected' : '' }}>{{ ucfirst($mod) }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Subject *</label>
            <input type="text" name="subject" class="form-control" value="{{ old('subject', $template->subject) }}" required placeholder="Order #{order_no} Confirmed">
        </div>

        <div class="mb-3">
            <label class="form-label">Body (HTML) *</label>
            <textarea name="body" class="form-control" rows="12" id="templateBody" required>{{ old('body', $template->body) }}</textarea>
            <small style="color:var(--text-muted-gold)">Use {variable_name} for dynamic content.</small>
        </div>

        <div class="mb-3">
            <label class="form-label">Variables List</label>
            <input type="text" name="variables_list" class="form-control" value="{{ old('variables_list', $template->variables_list) }}" placeholder="order_no, customer_name, amount, date">
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
