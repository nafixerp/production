@extends('layouts.app')
@section('title','Database Backup')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-database me-2"></i>Database Backup</h5>
    <form method="POST" action="{{ route('backup.create') }}" onsubmit="return confirm('Start a new backup now?')">
        @csrf
        <input type="hidden" name="type" value="manual">
        <button type="submit" class="btn-gold">
            <i class="bi bi-cloud-arrow-up me-1"></i>Create Backup Now
        </button>
    </form>
</div>

<div class="card-gold">
    <table class="table-gold w-100">
        <thead>
            <tr>
                <th>#</th><th>Type</th><th>Filename</th><th>Size</th><th>Status</th>
                <th>Started</th><th>Completed</th><th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($backups as $backup)
            <tr>
                <td>{{ $backup->id }}</td>
                <td><span class="badge-gold">{{ ucfirst($backup->backup_type) }}</span></td>
                <td style="font-size:.75rem;font-family:monospace">{{ $backup->file_name }}</td>
                <td>{{ $backup->file_size_mb }} MB</td>
                <td>
                    @php
                        $sc = match($backup->status) { 'completed'=>'#4ade80','running'=>'#fb923c', default=>'#f87171' };
                    @endphp
                    <span style="color:{{ $sc }};font-weight:600">
                        @if($backup->status === 'running') <i class="bi bi-arrow-clockwise"></i> @endif
                        {{ ucfirst($backup->status) }}
                    </span>
                </td>
                <td style="font-size:.75rem">{{ $backup->started_at?->format('d/m/Y H:i') }}</td>
                <td style="font-size:.75rem">{{ $backup->completed_at?->format('d/m/Y H:i') ?? '—' }}</td>
                <td>
                    @if($backup->status === 'completed')
                    <a href="{{ route('backup.download', $backup->id) }}" class="btn-outline-gold btn-sm-gold me-1" title="Download">
                        <i class="bi bi-download"></i>
                    </a>
                    <form method="POST" action="/backup/{{ $backup->id }}/restore" class="d-inline"
                          onsubmit="return confirm('WARNING: This will overwrite all current data. Are you sure?')">
                        @csrf
                        <button class="btn-outline-gold btn-sm-gold" style="color:#fb923c;border-color:#fb923c" title="Restore">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center" style="color:#666;padding:30px">No backups created yet.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-3">{{ $backups->links() }}</div>
</div>

<div class="card-gold mt-3" style="font-size:.8rem">
    <div class="card-header-gold"><h5>Backup Information</h5></div>
    <ul style="color:#a0987c;list-style:none;padding:0;margin:0">
        <li class="mb-1"><i class="bi bi-info-circle me-2 text-gold"></i>Backups are stored in <code>storage/app/backups/</code></li>
        <li class="mb-1"><i class="bi bi-info-circle me-2 text-gold"></i>Restore operation will overwrite all current data</li>
        <li class="mb-1"><i class="bi bi-info-circle me-2 text-gold"></i>Requires <code>mysqldump</code> to be available on the server</li>
    </ul>
</div>
@endsection
