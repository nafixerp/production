@extends('layouts.app')
@section('title','API Logs — ' . $apiClient->name)
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-list-ul me-2"></i>API Logs — {{ $apiClient->name }}</h5>
    <a href="{{ route('api-clients.index') }}" class="btn-outline-gold btn-sm-gold"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>

<div class="card-gold">
    <table class="table-gold w-100">
        <thead>
            <tr>
                <th>Time</th>
                <th>Method</th>
                <th>Endpoint</th>
                <th>Status</th>
                <th>Response (ms)</th>
                <th>IP</th>
                <th>Request Body</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
            <tr>
                <td style="white-space:nowrap;font-size:.75rem">{{ $log->created_at?->format('d/m H:i:s') }}</td>
                <td>
                    @php
                        $mc = match($log->method) { 'GET'=>'#4ade80','POST'=>'#60a5fa','PUT'=>'#fb923c','DELETE'=>'#f87171', default=>'#ccc' };
                    @endphp
                    <span style="color:{{ $mc }};font-weight:700;font-size:.75rem">{{ $log->method }}</span>
                </td>
                <td style="font-size:.78rem;max-width:260px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $log->endpoint }}</td>
                <td>
                    @php $code = $log->response_code; @endphp
                    <span style="color:{{ $code < 300 ? '#4ade80' : ($code < 500 ? '#fb923c' : '#f87171') }};font-weight:700">{{ $code }}</span>
                </td>
                <td>{{ $log->response_time_ms }} ms</td>
                <td style="font-size:.75rem">{{ $log->ip_address }}</td>
                <td style="font-size:.72rem;max-width:180px">
                    @if($log->request_body)
                        <button class="btn-outline-gold btn-sm-gold" onclick="alert(JSON.stringify({{ json_encode($log->request_body) }}, null, 2))">
                            <i class="bi bi-braces"></i>
                        </button>
                    @else
                        —
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center" style="color:#666;padding:30px">No API logs found.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-3">{{ $logs->links() }}</div>
</div>
@endsection
