<?php

declare(strict_types=1);

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;

/**
 * Registra els avisos de feedback enviats per tutoria i grup.
 */
class TutoriaFeedbackNotification extends Model
{
    /** @var string */
    protected $table = 'tutoria_feedback_notifications';

    /** @var array<int, string> */
    protected $fillable = [
        'idTutoria',
        'idGrupo',
    ];
}
