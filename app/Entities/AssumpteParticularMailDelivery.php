<?php

declare(strict_types=1);

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;

/** Registre persistent i idempotent d'un avís d'assumptes particulars. */
class AssumpteParticularMailDelivery extends Model
{
    public const URGENT = 'urgent';
    public const DENEGADA = 'denegada';
    public const AUTORITZADA = 'autoritzada';

    public const PENDENT = 'pendent';
    public const ENVIANT = 'enviant';
    public const ENVIADA = 'enviada';
    public const ERROR = 'error';

    protected $table = 'assumpte_particular_mail_deliveries';

    protected $fillable = [
        'curs',
        'peticio_id',
        'tipus',
        'destinatari_dni',
        'destinatari_email',
        'estat',
        'intents',
        'error',
        'last_attempt_at',
        'sent_at',
    ];

    protected $casts = [
        'last_attempt_at' => 'datetime',
        'sent_at' => 'datetime',
    ];
}
