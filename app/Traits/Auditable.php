<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    public static function bootAuditable()
    {
        static::created(function ($model) {
            self::logAudit($model, 'create');
        });

        static::updated(function ($model) {
            self::logAudit($model, 'update');
        });

        static::deleted(function ($model) {
            self::logAudit($model, 'delete');
        });
    }

    protected static function logAudit($model, $action)
    {
        // Only log if a user is authenticated
        if (Auth::check()) {
            $details = [];
            $details['record_id'] = $model->getKey();

            if ($action === 'create') {
                $details['new'] = $model->getAttributes();
            } elseif ($action === 'update') {
                $details['old'] = array_intersect_key($model->getOriginal(), $model->getDirty());
                $details['new'] = $model->getDirty();
            } elseif ($action === 'delete') {
                $details['old'] = $model->getAttributes();
            }

            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => $action,
                'module' => class_basename($model),
                'details' => $details
            ]);
        }
    }
}
