@extends('layouts.app')
@section('title','System Settings')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-sliders me-2"></i>System Settings</h5>
</div>

<form method="POST" action="{{ route('system-settings.update') }}">
    @csrf

    <ul class="nav nav-tabs mb-3" id="settingsTabs" style="border-color:var(--border-gold)">
        @foreach(array_keys($categories) as $i => $cat)
        <li class="nav-item">
            <button class="nav-link {{ $i === 0 ? 'active' : '' }}" type="button"
                data-bs-toggle="tab" data-bs-target="#cat-{{ Str::slug($cat) }}"
                style="{{ $i === 0 ? 'color:var(--gold);background:rgba(212,175,55,.1);border-color:var(--border-gold)' : 'color:#888' }}">
                {{ $cat }}
            </button>
        </li>
        @endforeach
    </ul>

    <div class="tab-content">
        @foreach($categories as $cat => $keys)
        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="cat-{{ Str::slug($cat) }}">
            <div class="card-gold">
                <div class="card-header-gold"><h5>{{ $cat }} Settings</h5></div>
                <table style="width:100%;border-collapse:collapse">
                    @foreach($keys as $key)
                    @php $setting = $allSettings[$key] ?? null; @endphp
                    <tr style="border-bottom:1px solid rgba(212,175,55,.07)">
                        <td style="padding:10px 0;width:40%">
                            <label class="form-label mb-0" style="font-size:.8rem">{{ ucwords(str_replace('_',' ',$key)) }}</label>
                            @if($setting?->description)
                                <div style="color:#666;font-size:.7rem">{{ $setting->description }}</div>
                            @endif
                        </td>
                        <td style="padding:8px 0 8px 20px">
                            @php $val = $setting?->setting_value ?? ''; @endphp
                            @if(in_array(substr($key, -8), ['enabled','allowed','required','_check']))
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="settings[{{ $key }}]" value="1" {{ $val ? 'checked' : '' }}>
                                </div>
                            @else
                                <input type="text" name="settings[{{ $key }}]" class="form-control" value="{{ $val }}" style="max-width:400px">
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </table>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-3">
        <button type="submit" class="btn-gold"><i class="bi bi-floppy me-1"></i>Save All Settings</button>
    </div>
</form>
@endsection
