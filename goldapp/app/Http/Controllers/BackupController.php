<?php

namespace App\Http\Controllers;

use App\Models\BackupLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BackupController extends Controller
{
    public function index()
    {
        $backups = BackupLog::orderByDesc('started_at')->paginate(20);
        return view('settings.backup.index', compact('backups'));
    }

    public function create(Request $request)
    {
        $type = $request->input('type', 'manual');
        $filename = 'backup_' . date('Ymd_His') . '.sql';
        $backupPath = storage_path('app/backups');
        if (!is_dir($backupPath)) {
            mkdir($backupPath, 0755, true);
        }

        $log = BackupLog::create([
            'backup_type' => $type,
            'file_name' => $filename,
            'file_size_mb' => 0,
            'status' => 'running',
            'started_at' => now(),
            'created_by' => auth()->id() ?? 1,
        ]);

        try {
            $db = config('database.connections.' . config('database.default'));
            $host = $db['host'] ?? '127.0.0.1';
            $port = $db['port'] ?? 3306;
            $dbname = $db['database'];
            $user = $db['username'];
            $pass = $db['password'];

            $filePath = $backupPath . '/' . $filename;
            $cmd = "mysqldump --host={$host} --port={$port} --user={$user} --password={$pass} {$dbname} > {$filePath} 2>&1";
            exec($cmd, $output, $returnCode);

            if ($returnCode !== 0 && !file_exists($filePath)) {
                throw new \RuntimeException('mysqldump failed. Code: ' . $returnCode);
            }

            $sizeMb = file_exists($filePath) ? round(filesize($filePath) / (1024 * 1024), 2) : 0;

            $log->update([
                'status' => 'completed',
                'completed_at' => now(),
                'file_size_mb' => $sizeMb,
            ]);

            return redirect()->route('backup.index')->with('success', "Backup completed: {$filename} ({$sizeMb} MB)");
        } catch (\Throwable $e) {
            $log->update(['status' => 'failed']);
            return redirect()->route('backup.index')->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }

    public function download(int $id): StreamedResponse
    {
        $log = BackupLog::findOrFail($id);
        $filePath = storage_path('app/backups/' . $log->file_name);

        abort_unless(file_exists($filePath), 404, 'Backup file not found on disk.');

        return response()->streamDownload(function () use ($filePath) {
            $fh = fopen($filePath, 'rb');
            while (!feof($fh)) {
                echo fread($fh, 8192);
                ob_flush();
                flush();
            }
            fclose($fh);
        }, $log->file_name, [
            'Content-Type' => 'application/sql',
            'Content-Length' => filesize($filePath),
        ]);
    }

    public function restore(Request $request, int $id)
    {
        $log = BackupLog::findOrFail($id);
        $filePath = storage_path('app/backups/' . $log->file_name);
        abort_unless(file_exists($filePath), 404, 'Backup file not found.');

        $db = config('database.connections.' . config('database.default'));
        $host = $db['host'] ?? '127.0.0.1';
        $port = $db['port'] ?? 3306;
        $dbname = $db['database'];
        $user = $db['username'];
        $pass = $db['password'];

        $cmd = "mysql --host={$host} --port={$port} --user={$user} --password={$pass} {$dbname} < {$filePath} 2>&1";
        exec($cmd, $output, $returnCode);

        if ($returnCode !== 0) {
            return redirect()->back()->with('error', 'Restore failed. Exit code: ' . $returnCode);
        }

        return redirect()->route('backup.index')->with('success', 'Database restored from: ' . $log->file_name);
    }
}
