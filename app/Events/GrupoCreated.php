<?php

namespace Intranet\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Intranet\Entities\GrupoTrabajo;

/**
 * Event de grup creat.
 */
class GrupoCreated
{

    use Dispatchable,
        InteractsWithSockets,
        SerializesModels;

    /**
     * @var GrupoTrabajo
     */
    public $grupo;

    /**
     * @param GrupoTrabajo $grupo
     */
    public function __construct(GrupoTrabajo $grupo)
    {
        $this->grupo = $grupo;
    }

}
