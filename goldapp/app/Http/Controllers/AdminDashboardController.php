<?php

namespace App\Http\Controllers;

use App\Models\BackupLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $health = $this->getSystemHealth();
        return view('settings.admin-dashboard.index', compact('health'));
    }

    protected function getSystemHealth(): array
    {
        // DB size
        $dbName = config('database.connections.' . config('database.default') . '.database');
        $dbSize = DB::select("
            SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb
            FROM information_schema.tables
            WHERE table_schema = ?
        ", [$dbName])[0]->size_mb ?? 0;

        // Last backup
        $lastBackup = BackupLog::where('status', 'completed')->latest('completed_at')->first();

        // Active users (last 30 min)
        $activeUsers = 0;
        try {
            $activeUsers = DB::table('users')
                ->where('updated_at', '>=', now()->subMinutes(30))
                ->count();
        } catch (\Throwable $e) {}

        // Recent logins
        $recentLogins = [];
        try {
            $recentLogins = DB::table('users')
                ->select('name', 'email', 'updated_at')
                ->orderByDesc('updated_at')
                ->limit(10)
                ->get();
        } catch (\Throwable $e) {}

        // Error count from API logs
        $errorCount = 0;
        try {
            $errorCount = DB::table('api_logs')
                ->where('response_code', '>=', 500)
                ->whereRaw('created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)')
                ->count();
        } catch (\Throwable $e) {}

        // Table counts
        $tableCounts = [];
        $tables = ['users', 'sales_orders', 'purchase_orders', 'production_orders', 'dispatch_orders'];
        foreach ($tables as $table) {
            try {
                $tableCounts[$table] = DB::table($table)->count();
            } catch (\Throwable $e) {
                $tableCounts[$table] = 'N/A';
            }
        }

        return [
            'db_size_mb' => $dbSize,
            'last_backup' => $lastBackup,
            'active_users' => $activeUsers,
            'recent_logins' => $recentLogins,
            'error_count_24h' => $errorCount,
            'table_counts' => $tableCounts,
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'server_time' => now()->toDateTimeString(),
            'disk_free_mb' => round(disk_free_space('/') / (1024 * 1024), 0),
        ];
    }
}
