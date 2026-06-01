@extends('layouts.app')
@section('title','Configure — ' . $meta['label'])
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="text-gold mb-0"><i class="bi {{ $meta['icon'] }} me-2"></i>{{ $meta['label'] }} — Configuration</h5>
    <a href="{{ route('integrations.index') }}" class="btn-outline-gold btn-sm-gold"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

<div class="card-gold" style="max-width:700px">
    <form method="POST" action="{{ route('integrations.save', $type) }}">
        @csrf

        <div class="mb-3">
            <label class="form-label">Provider *</label>
            <select name="provider" class="form-select" required>
                <option value="">— Select Provider —</option>
                @foreach($meta['providers'] as $p)
                    <option value="{{ $p }}" {{ old('provider', $setting->provider ?? '') === $p ? 'selected' : '' }}>{{ $p }}</option>
                @endforeach
            </select>
        </div>

        @php $config = old('config', $setting->config ?? []); @endphp

        @if($type === 'gst')
        <div class="mb-3">
            <label class="form-label">GSTIN</label>
            <input type="text" name="config[gstin]" class="form-control" value="{{ $config['gstin'] ?? '' }}" placeholder="22AAAAA0000A1Z5">
        </div>
        <div class="mb-3">
            <label class="form-label">API Username</label>
            <input type="text" name="config[api_username]" class="form-control" value="{{ $config['api_username'] ?? '' }}">
        </div>
        <div class="mb-3">
            <label class="form-label">API Password</label>
            <input type="password" name="config[api_password]" class="form-control" placeholder="••••••••">
        </div>
        <div class="mb-3">
            <label class="form-label">Client ID</label>
            <input type="text" name="config[client_id]" class="form-control" value="{{ $config['client_id'] ?? '' }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Client Secret</label>
            <input type="password" name="config[client_secret]" class="form-control" placeholder="••••••••">
        </div>
        @elseif($type === 'payment_gateway')
        <div class="mb-3">
            <label class="form-label">API Key</label>
            <input type="text" name="config[api_key]" class="form-control" value="{{ $config['api_key'] ?? '' }}">
        </div>
        <div class="mb-3">
            <label class="form-label">API Secret</label>
            <input type="password" name="config[api_secret]" class="form-control" placeholder="••••••••">
        </div>
        <div class="mb-3">
            <label class="form-label">Webhook Secret</label>
            <input type="text" name="config[webhook_secret]" class="form-control" value="{{ $config['webhook_secret'] ?? '' }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Mode</label>
            <select name="config[mode]" class="form-select">
                <option value="sandbox" {{ ($config['mode'] ?? '') === 'sandbox' ? 'selected' : '' }}>Sandbox (Test)</option>
                <option value="live" {{ ($config['mode'] ?? '') === 'live' ? 'selected' : '' }}>Live (Production)</option>
            </select>
        </div>
        @elseif($type === 'courier')
        <div class="mb-3">
            <label class="form-label">API Token</label>
            <input type="text" name="config[api_token]" class="form-control" value="{{ $config['api_token'] ?? '' }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Pickup Pincode</label>
            <input type="text" name="config[pickup_pincode]" class="form-control" value="{{ $config['pickup_pincode'] ?? '' }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Client Code</label>
            <input type="text" name="config[client_code]" class="form-control" value="{{ $config['client_code'] ?? '' }}">
        </div>
        @elseif($type === 'ecommerce')
        <div class="mb-3">
            <label class="form-label">Seller ID / Store ID</label>
            <input type="text" name="config[seller_id]" class="form-control" value="{{ $config['seller_id'] ?? '' }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Access Token</label>
            <input type="text" name="config[access_token]" class="form-control" value="{{ $config['access_token'] ?? '' }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Marketplace URL</label>
            <input type="text" name="config[marketplace_url]" class="form-control" value="{{ $config['marketplace_url'] ?? '' }}" placeholder="https://...">
        </div>
        @else
        <div class="mb-3">
            <label class="form-label">API Endpoint</label>
            <input type="text" name="config[api_endpoint]" class="form-control" value="{{ $config['api_endpoint'] ?? '' }}" placeholder="https://...">
        </div>
        <div class="mb-3">
            <label class="form-label">API Key</label>
            <input type="text" name="config[api_key]" class="form-control" value="{{ $config['api_key'] ?? '' }}">
        </div>
        @endif

        <div class="mb-4">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="isActive"
                    {{ old('is_active', $setting->is_active ?? 1) ? 'checked' : '' }}>
                <label class="form-check-label" for="isActive" style="color:#ccc">Enable this integration</label>
            </div>
        </div>

        <button type="submit" class="btn-gold"><i class="bi bi-floppy me-1"></i>Save Configuration</button>
    </form>
</div>
@endsection
