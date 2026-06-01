@extends('layouts.app')
@section('title','API Clients')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="text-gold mb-0"><i class="bi bi-key me-2"></i>API Clients</h5>
    <a href="{{ route('api-clients.create') }}" class="btn-gold btn-sm-gold"><i class="bi bi-plus-circle me-1"></i>New Client</a>
</div>

<div class="card-gold">
    <table class="table-gold w-100">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Client Key</th>
                <th>Permissions</th>
                <th>Rate Limit</th>
                <th>Status</th>
                <th>Last Used</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($clients as $client)
            <tr>
                <td>{{ $client->id }}</td>
                <td>{{ $client->name }}</td>
                <td>
                    <code class="text-gold" style="font-size:.75rem">
                        {{ substr($client->client_key,0,12) }}••••••••
                    </code>
                </td>
                <td>
                    @foreach((array)($client->permissions ?? []) as $perm)
                        <span class="badge-gold me-1">{{ $perm }}</span>
                    @endforeach
                </td>
                <td>{{ $client->rate_limit_per_minute }}/min</td>
                <td>
                    @if($client->is_active)
                        <span style="color:#4ade80"><i class="bi bi-circle-fill" style="font-size:.5rem"></i> Active</span>
                    @else
                        <span style="color:#f87171"><i class="bi bi-circle-fill" style="font-size:.5rem"></i> Inactive</span>
                    @endif
                </td>
                <td>{{ $client->last_used_at ? $client->last_used_at->diffForHumans() : '—' }}</td>
                <td>
                    <a href="{{ route('api-clients.edit', $client) }}" class="btn-outline-gold btn-sm-gold me-1"><i class="bi bi-pencil"></i></a>
                    <a href="{{ route('api-clients.logs', $client) }}" class="btn-outline-gold btn-sm-gold me-1"><i class="bi bi-list-ul"></i> Logs</a>
                    <form method="POST" action="{{ route('api-clients.destroy', $client) }}" class="d-inline"
                          onsubmit="return confirm('Delete this client?')">
                        @csrf @method('DELETE')
                        <button class="btn-outline-gold btn-sm-gold" style="color:#f87171;border-color:#f87171"><i class="bi bi-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center" style="color:#666;padding:30px">No API clients configured yet.</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-3">{{ $clients->links() }}</div>
</div>
@endsection
