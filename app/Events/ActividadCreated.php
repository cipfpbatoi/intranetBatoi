<?php

namespace Intranet\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Intranet\Entities\Actividad;

/**
 * Event d'activitat creada.
 */
class ActividadCreated
{

    use Dispatchable,
        InteractsWithSockets,
        SerializesModels;

    /**
     * @var Actividad
     */
    public $actividad;

    /**
     * @param Actividad $actividad
     */
    public function __construct(Actividad $actividad)
    {
        $this->actividad = $actividad;
    }

}
