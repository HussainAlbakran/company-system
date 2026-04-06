<?php

namespace App\Helpers;

use App\Models\AuditLog;

class AuditHelper
{
    public static function log($action, $model, $model_id = null, $description = null)
    {
        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'model' => $model,
            'model_id' => $model_id,
            'description' => $description,
        ]);
    }
}