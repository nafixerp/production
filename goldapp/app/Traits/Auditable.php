<?php
namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait Auditable {
    protected static function bootAuditable() {
        static::created(function ($model) {
            self::logAudit('create', $model, null, $model->toArray());
        });
        static::updated(function ($model) {
            self::logAudit('update', $model, $model->getOriginal(), $model->toArray());
        });
        static::deleted(function ($model) {
            self::logAudit('delete', $model, $model->toArray(), null);
        });
    }

    private static function logAudit($action, $model, $old, $new) {
        try {
            DB::table('audit_logs')->insert([
                'user_id'    => auth()->id(),
                'user_name'  => auth()->check() ? auth()->user()->name : 'System',
                'action'     => $action,
                'module'     => class_basename($model),
                'record_id'  => $model->id,
                'old_values' => $old ? json_encode($old) : null,
                'new_values' => $new ? json_encode($new) : null,
                'ip_address' => request()->ip(),
                'user_agent' => substr(request()->userAgent() ?? '', 0, 255),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            // Silently fail to not interrupt business operations
        }
    }
}
