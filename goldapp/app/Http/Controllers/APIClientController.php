<?php

namespace App\Http\Controllers;

use App\Models\ApiClient;
use App\Models\ApiLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class APIClientController extends Controller
{
    public function index()
    {
        $clients = ApiClient::orderByDesc('created_at')->paginate(20);
        return view('integrations.api-clients.index', compact('clients'));
    }

    public function create()
    {
        return view('integrations.api-clients.form', ['client' => new ApiClient()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'permissions' => 'nullable|array',
            'rate_limit_per_minute' => 'nullable|integer|min:1|max:1000',
            'is_active' => 'nullable|boolean',
        ]);

        $clientKey = 'ck_' . Str::random(32);
        $clientSecret = 'cs_' . Str::random(48);

        ApiClient::create([
            'name' => $data['name'],
            'client_key' => $clientKey,
            'client_secret' => Hash::make($clientSecret),
            'permissions' => $data['permissions'] ?? [],
            'rate_limit_per_minute' => $data['rate_limit_per_minute'] ?? 60,
            'is_active' => $data['is_active'] ?? 1,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('api-clients.index')
            ->with('success', "API client created. Secret: {$clientSecret} (save this — it won't be shown again)");
    }

    public function show(ApiClient $apiClient)
    {
        return redirect()->route('api-clients.edit', $apiClient);
    }

    public function edit(ApiClient $apiClient)
    {
        return view('integrations.api-clients.form', ['client' => $apiClient]);
    }

    public function update(Request $request, ApiClient $apiClient)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'permissions' => 'nullable|array',
            'rate_limit_per_minute' => 'nullable|integer|min:1|max:1000',
            'is_active' => 'nullable|boolean',
        ]);

        $apiClient->update([
            'name' => $data['name'],
            'permissions' => $data['permissions'] ?? [],
            'rate_limit_per_minute' => $data['rate_limit_per_minute'] ?? 60,
            'is_active' => $data['is_active'] ?? 0,
        ]);

        return redirect()->route('api-clients.index')->with('success', 'API client updated.');
    }

    public function destroy(ApiClient $apiClient)
    {
        $apiClient->delete();
        return redirect()->route('api-clients.index')->with('success', 'API client deleted.');
    }

    public function logs(ApiClient $apiClient)
    {
        $logs = ApiLog::where('client_id', $apiClient->id)->orderByDesc('created_at')->paginate(50);
        return view('integrations.api-clients.logs', compact('apiClient', 'logs'));
    }

    public function regenerateSecret(ApiClient $apiClient)
    {
        $newSecret = 'cs_' . Str::random(48);
        $apiClient->update(['client_secret' => Hash::make($newSecret)]);
        return redirect()->route('api-clients.index')
            ->with('success', "Secret regenerated: {$newSecret} (save this — it won't be shown again)");
    }
}
