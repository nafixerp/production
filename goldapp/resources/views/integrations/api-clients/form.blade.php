@extends('layouts.app')
@section('title', $client->exists ? 'Edit API Client' : 'New API Client')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-key me-2"></i>{{ $client->exists ? 'Edit API Client' : 'New API Client' }}</h5>
    <a href="{{ route('api-clients.index') }}" class="btn-outline-gold btn-sm-gold"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

<div class="card-gold" style="max-width:680px">
    <form method="POST" action="{{ $client->exists ? route('api-clients.update', $client) : route('api-clients.store') }}">
        @csrf
        @if($client->exists) @method('PUT') @endif

        <div class="mb-3">
            <label class="form-label">Client Name *</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $client->name) }}" required placeholder="e.g. Mobile App, Partner Portal">
        </div>

        @if($client->exists)
        <div class="mb-3">
            <label class="form-label">Client Key (read-only)</label>
            <input type="text" class="form-control" value="{{ $client->client_key }}" readonly>
            <small style="color:var(--text-muted-gold)">Key is assigned at creation and cannot be changed.</small>
        </div>
        @endif

        <div class="mb-3">
            <label class="form-label">Rate Limit (requests/minute)</label>
            <input type="number" name="rate_limit_per_minute" class="form-control" value="{{ old('rate_limit_per_minute', $client->rate_limit_per_minute ?? 60) }}" min="1" max="1000">
        </div>

        <div class="mb-3">
            <label class="form-label">Permissions</label>
            <div class="row g-2">
                @foreach(['read:stock','write:orders','read:invoices','write:dispatch','read:reports','admin:api'] as $perm)
                <div class="col-6 col-md-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $perm }}" id="perm_{{ $loop->index }}"
                            {{ in_array($perm, (array)($client->permissions ?? [])) ? 'checked' : '' }}>
                        <label class="form-check-label" for="perm_{{ $loop->index }}" style="color:#ccc;font-size:.8rem">{{ $perm }}</label>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="mb-4">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive"
                    {{ old('is_active', $client->is_active ?? 1) ? 'checked' : '' }}>
                <label class="form-check-label" for="isActive" style="color:#ccc">Active</label>
            </div>
        </div>

        @if(!$client->exists)
        <div class="alert-gold-success mb-3" style="font-size:.8rem">
            <i class="bi bi-info-circle me-1"></i>
            Client key and secret will be generated automatically. Save the secret immediately — it won't be shown again.
        </div>
        @endif

        <button type="submit" class="btn-gold">
            <i class="bi bi-check2 me-1"></i>{{ $client->exists ? 'Update Client' : 'Create Client' }}
        </button>
    </form>
</div>
@endsection
