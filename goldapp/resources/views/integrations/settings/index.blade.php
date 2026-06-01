@extends('layouts.app')
@section('title','Integration Settings')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-puzzle me-2"></i>Integration Settings</h5>
</div>

<div class="row g-3">
    @foreach($types as $typeKey => $meta)
    @php $setting = $settings[$typeKey] ?? null; @endphp
    <div class="col-md-6 col-xl-4">
        <div class="card-gold h-100">
            <div class="d-flex align-items-center mb-3">
                <i class="bi {{ $meta['icon'] }} me-2 text-gold" style="font-size:1.5rem"></i>
                <div>
                    <div style="color:var(--gold);font-weight:600">{{ $meta['label'] }}</div>
                    @if($setting)
                        <small style="color:var(--text-muted-gold)">{{ $setting->provider }}</small>
                    @else
                        <small style="color:#666">Not configured</small>
                    @endif
                </div>
                <div class="ms-auto">
                    @if($setting && $setting->is_active)
                        <span style="color:#4ade80;font-size:.72rem"><i class="bi bi-circle-fill" style="font-size:.5rem"></i> Active</span>
                    @else
                        <span style="color:#f87171;font-size:.72rem"><i class="bi bi-circle-fill" style="font-size:.5rem"></i> Inactive</span>
                    @endif
                </div>
            </div>

            @if($setting)
            <div style="font-size:.75rem;color:#999;margin-bottom:12px">
                Last sync: {{ $setting->last_sync_at ? $setting->last_sync_at->diffForHumans() : 'Never' }}
            </div>
            @endif

            <div class="d-flex gap-2">
                <a href="{{ route('integrations.configure', $typeKey) }}" class="btn-outline-gold btn-sm-gold flex-fill text-center">
                    <i class="bi bi-gear me-1"></i>Configure
                </a>
                @if($setting)
                <button onclick="testIntegration('{{ $typeKey }}')" class="btn-outline-gold btn-sm-gold">
                    <i class="bi bi-wifi"></i>
                </button>
                <button onclick="syncIntegration('{{ $typeKey }}')" class="btn-outline-gold btn-sm-gold">
                    <i class="bi bi-arrow-clockwise"></i>
                </button>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>

<div id="toast-msg" style="display:none;position:fixed;bottom:20px;right:20px;background:#13132a;border:1px solid var(--border-gold);padding:12px 20px;border-radius:8px;color:var(--gold);z-index:9999"></div>
@endsection
@push('scripts')
<script>
function testIntegration(type) {
    fetch('/integrations/test/' + type, {method:'POST', headers:{'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content}})
        .then(r => r.json()).then(data => showToast(data.message || 'Test done'));
}
function syncIntegration(type) {
    fetch('/integrations/sync/' + type, {method:'POST', headers:{'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content}})
        .then(r => r.json()).then(data => showToast('Synced at ' + (data.synced_at || '')));
}
function showToast(msg) {
    const t = document.getElementById('toast-msg');
    t.textContent = msg; t.style.display = 'block';
    setTimeout(() => t.style.display = 'none', 3500);
}
</script>
@endpush
