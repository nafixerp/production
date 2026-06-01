@extends('layouts.app')
@section('title','Admin Dashboard — System Health')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-activity me-2"></i>System Health Dashboard</h5>
    <small style="color:var(--text-muted-gold)">{{ $health['server_time'] }}</small>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-label">Database Size</div>
            <div class="stat-value">{{ $health['db_size_mb'] }} MB</div>
            <i class="bi bi-database stat-icon"></i>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-label">Active Users (30m)</div>
            <div class="stat-value">{{ $health['active_users'] }}</div>
            <i class="bi bi-people stat-icon"></i>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-label">API Errors (24h)</div>
            <div class="stat-value" style="color:{{ $health['error_count_24h'] > 0 ? '#f87171' : '#4ade80' }}">
                {{ $health['error_count_24h'] }}
            </div>
            <i class="bi bi-bug stat-icon"></i>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-label">Free Disk Space</div>
            <div class="stat-value">{{ number_format($health['disk_free_mb']) }} MB</div>
            <i class="bi bi-hdd stat-icon"></i>
        </div>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-6">
        <div class="card-gold h-100">
            <div class="card-header-gold"><h5>System Info</h5></div>
            <table style="width:100%;font-size:.82rem">
                <tr style="border-bottom:1px solid rgba(212,175,55,.07)">
                    <td style="color:var(--text-muted-gold);padding:7px 0">PHP Version</td>
                    <td><code class="text-gold">{{ $health['php_version'] }}</code></td>
                </tr>
                <tr style="border-bottom:1px solid rgba(212,175,55,.07)">
                    <td style="color:var(--text-muted-gold);padding:7px 0">Laravel Version</td>
                    <td><code class="text-gold">{{ $health['laravel_version'] }}</code></td>
                </tr>
                <tr style="border-bottom:1px solid rgba(212,175,55,.07)">
                    <td style="color:var(--text-muted-gold);padding:7px 0">Last Backup</td>
                    <td>
                        @if($health['last_backup'])
                            <span style="color:#4ade80">{{ $health['last_backup']->completed_at?->diffForHumans() }}</span>
                            <small style="color:#888;margin-left:6px">{{ $health['last_backup']->file_size_mb }} MB</small>
                        @else
                            <span style="color:#f87171">Never backed up</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td style="color:var(--text-muted-gold);padding:7px 0">Server Time</td>
                    <td>{{ $health['server_time'] }}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card-gold h-100">
            <div class="card-header-gold"><h5>Record Counts</h5></div>
            <table style="width:100%;font-size:.82rem">
                @foreach($health['table_counts'] as $table => $count)
                <tr style="border-bottom:1px solid rgba(212,175,55,.07)">
                    <td style="color:var(--text-muted-gold);padding:7px 0">{{ ucwords(str_replace('_',' ',$table)) }}</td>
                    <td style="color:var(--gold);font-weight:600">{{ is_numeric($count) ? number_format($count) : $count }}</td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>
</div>

<div class="card-gold">
    <div class="card-header-gold"><h5>Recent User Activity</h5></div>
    <table class="table-gold w-100">
        <thead>
            <tr><th>Name</th><th>Email</th><th>Last Active</th></tr>
        </thead>
        <tbody>
            @forelse($health['recent_logins'] as $user)
            <tr>
                <td>{{ $user->name }}</td>
                <td style="font-size:.78rem">{{ $user->email }}</td>
                <td style="font-size:.78rem">{{ \Carbon\Carbon::parse($user->updated_at)->diffForHumans() }}</td>
            </tr>
            @empty
            <tr><td colspan="3" style="color:#666;padding:12px">No recent activity.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
