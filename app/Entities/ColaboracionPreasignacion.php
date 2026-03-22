<?php

namespace Intranet\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Reserva o preassignació provisional d'un alumne sobre una col·laboració.
 *
 * Esta entitat no substituïx la FCT real; només representa el pas previ de
 * proposta o reserva dins del flux del tutor.
 */
class ColaboracionPreasignacion extends Model
{
    /**
     * Estats que compten com a reserva activa de capacitat.
     *
     * @var array<int, string>
     */
    public const ACTIVE_STATES = ['proposta', 'reservada'];

    /**
     * Estats disponibles en el primer tall funcional.
     *
     * @var array<int, string>
     */
    public const STATES = ['proposta', 'reservada', 'descartada', 'convertida'];

    protected $table = 'colaboracion_preasignaciones';

    protected $fillable = [
        'idColaboracion',
        'idAlumno',
        'idProfesor',
        'estado',
        'observaciones',
    ];

    /**
     * Retorna la col·laboració vinculada.
     */
    public function Colaboracion(): BelongsTo
    {
        return $this->belongsTo(Colaboracion::class, 'idColaboracion', 'id');
    }

    /**
     * Retorna l'alumne preassignat.
     */
    public function Alumno(): BelongsTo
    {
        return $this->belongsTo(Alumno::class, 'idAlumno', 'nia');
    }

    /**
     * Retorna el professor que crea o manté la reserva.
     */
    public function Profesor(): BelongsTo
    {
        return $this->belongsTo(Profesor::class, 'idProfesor', 'dni');
    }

    /**
     * Indica si la preassignació ocupa una plaça de la col·laboració.
     */
    public function isActive(): bool
    {
        return in_array((string) $this->estado, self::ACTIVE_STATES, true);
    }
}
