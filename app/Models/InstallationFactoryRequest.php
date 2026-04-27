<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InstallationFactoryRequest extends Model
{
    public const STATUS_DRAFT = 'draft';

    public const STATUS_SUBMITTED = 'submitted';

    public const STATUS_RECEIVED = 'received';

    public const STATUS_PROCESSING = 'processing';

    public const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'project_id',
        'created_by',
        'status',
        'notes',
        'submitted_at',
        'factory_first_opened_at',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'factory_first_opened_at' => 'datetime',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(InstallationFactoryRequestItem::class, 'request_id')->orderBy('id');
    }

    public static function statusLabel(string $status): string
    {
        return match ($status) {
            self::STATUS_DRAFT => 'مسودة',
            self::STATUS_SUBMITTED => 'مرسل للمصنع',
            self::STATUS_RECEIVED => 'مستلم',
            self::STATUS_PROCESSING => 'قيد التنفيذ',
            self::STATUS_COMPLETED => 'مكتمل',
            default => $status,
        };
    }
}
