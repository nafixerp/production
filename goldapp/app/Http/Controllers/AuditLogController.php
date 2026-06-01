<?php
namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuditLogController extends Controller {
    public function index(Request $request) {
        $q = AuditLog::query();

        if ($request->filled('user_name')) {
            $q->where('user_name', 'like', '%' . $request->user_name . '%');
        }
        if ($request->filled('module')) {
            $q->where('module', $request->module);
        }
        if ($request->filled('action')) {
            $q->where('action', $request->action);
        }
        if ($request->filled('from')) {
            $q->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $q->whereDate('created_at', '<=', $request->to);
        }

        $logs = $q->orderByDesc('created_at')->paginate(30)->withQueryString();

        $modules = AuditLog::distinct()->pluck('module')->sort()->values();
        $actions = ['login', 'logout', 'create', 'update', 'delete', 'view', 'export', 'approve'];

        return view('admin.audit-logs.index', compact('logs', 'modules', 'actions'));
    }

    public function show($id) {
        $log = AuditLog::findOrFail($id);
        return view('admin.audit-logs.show', compact('log'));
    }
}
