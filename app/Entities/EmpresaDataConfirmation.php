<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Sol·licitud temporal perquè una empresa confirme les dades d'FCT sense iniciar sessió.
 */
class EmpresaDataConfirmation extends Model
{
    protected $table = 'empresa_data_confirmations';

    protected $fillable = [
        'empresa_id',
        'tutor_dni',
        'recipient_email',
        'token_hash',
        'colaboracion_ids',
        'centro_ids',
        'sent_at',
        'expires_at',
        'confirmed_at',
    ];

    protected $casts = [
        'colaboracion_ids' => 'array',
        'centro_ids' => 'array',
        'sent_at' => 'datetime',
        'expires_at' => 'datetime',
        'confirmed_at' => 'datetime',
    ];

    /**
     * Empresa a la qual correspon la sol·licitud.
     */
    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    /**
     * Tutor que va enviar la sol·licitud de confirmació.
     */
    public function tutor(): BelongsTo
    {
        return $this->belongsTo(Profesor::class, 'tutor_dni', 'dni');
    }

    /**
     * Indica si el token encara permet modificar les dades.
     */
    public function isActionable(): bool
    {
        return $this->confirmed_at === null && $this->expires_at->isFuture();
    }
}
