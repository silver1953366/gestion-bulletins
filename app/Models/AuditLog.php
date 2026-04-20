<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditLog extends Model
{
    protected $table = 'audit_logs';

    protected $fillable = [
        'user_id',
        'action',
        'model',
        'model_id',
        'old_value',
        'new_value',
        'ip_address'
    ];

    protected $casts = [
        'old_value' => 'array',
        'new_value' => 'array',
        'created_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function log($action, $model, $modelId, $oldValue = null, $newValue = null)
    {
        return static::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'model' => $model,
            'model_id' => $modelId,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'ip_address' => request()->ip()
        ]);
    }
}