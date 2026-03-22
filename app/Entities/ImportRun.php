<?php

declare(strict_types=1);

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;

class ImportRun extends Model
{
    protected $table = 'import_runs';

    protected $fillable = [
        'type',
        'status',
        'file_path',
        'options',
        'progress',
        'message',
        'error',
        'started_at',
        'finished_at',
        'failed_at',
    ];

    protected $casts = [
        'options' => 'array',
        'progress' => 'integer',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
        'failed_at' => 'datetime',
    ];
}

