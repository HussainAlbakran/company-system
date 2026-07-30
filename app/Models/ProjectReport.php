<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectReport extends Model
{
    public const TYPE_PROJECT = 'project';

    public const TYPE_WEEKLY = 'weekly';

    public const TYPE_FINANCIAL_DISTRESS = 'financial_distress';

    public const TYPE_SITE_ACCIDENT = 'site_accident';

    public const TYPE_DELAY = 'delay';

    public const TYPES = [
        self::TYPE_WEEKLY,
        self::TYPE_PROJECT,
        self::TYPE_FINANCIAL_DISTRESS,
        self::TYPE_SITE_ACCIDENT,
        self::TYPE_DELAY,
    ];

    protected $fillable = [
        'project_id',
        'uploaded_by',
        'report_type',
        'report_date',
        'original_name',
        'stored_path',
        'mime_type',
        'file_size',
        'notes',
    ];

    protected $casts = [
        'report_date' => 'date',
        'file_size' => 'integer',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function typeLabel(): string
    {
        return __('project_reports.types.'.$this->report_type);
    }
}
