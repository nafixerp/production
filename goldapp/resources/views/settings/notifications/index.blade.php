@extends('layouts.app')
@section('title','Notification Preferences')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-bell me-2"></i>Notification Preferences</h5>
</div>

<form method="POST" action="{{ route('notification-prefs.update') }}">
    @csrf
    <div class="card-gold">
        <div style="overflow-x:auto">
            <table class="table-gold w-100">
                <thead>
                    <tr>
                        <th style="min-width:200px">Module / Event</th>
                        <th class="text-center"><i class="bi bi-envelope me-1"></i>Email</th>
                        <th class="text-center"><i class="bi bi-phone me-1"></i>SMS</th>
                        <th class="text-center"><i class="bi bi-bell me-1"></i>Push</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($modules as $module => $events)
                    <tr style="background:rgba(212,175,55,.06)">
                        <td colspan="4" style="color:var(--gold);font-weight:700;font-size:.78rem;letter-spacing:1px;text-transform:uppercase;padding:10px 12px">
                            <i class="bi bi-folder2 me-2"></i>{{ ucfirst($module) }}
                        </td>
                    </tr>
                    @foreach($events as $event)
                    @php $pref = $prefs[$module][$event] ?? null; @endphp
                    <tr>
                        <td style="padding-left:28px;font-size:.8rem">{{ ucwords(str_replace('_',' ',$event)) }}</td>
                        <td class="text-center">
                            <input type="checkbox" name="prefs[{{ $module }}.{{ $event }}][email]" value="1"
                                {{ $pref?->email ? 'checked' : '' }}
                                style="accent-color:var(--gold);width:16px;height:16px;cursor:pointer">
                        </td>
                        <td class="text-center">
                            <input type="checkbox" name="prefs[{{ $module }}.{{ $event }}][sms]" value="1"
                                {{ $pref?->sms ? 'checked' : '' }}
                                style="accent-color:var(--gold);width:16px;height:16px;cursor:pointer">
                        </td>
                        <td class="text-center">
                            <input type="checkbox" name="prefs[{{ $module }}.{{ $event }}][push]" value="1"
                                {{ $pref?->push ? 'checked' : '' }}
                                style="accent-color:var(--gold);width:16px;height:16px;cursor:pointer">
                        </td>
                    </tr>
                    @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">
        <button type="submit" class="btn-gold"><i class="bi bi-floppy me-1"></i>Save Preferences</button>
    </div>
</form>
@endsection
