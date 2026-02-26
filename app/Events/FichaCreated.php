<?php

namespace Intranet\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Intranet\Entities\Falta_profesor;

/**
 * Event de fitxatge creat.
 */
class FichaCreated
{

    use Dispatchable,
        InteractsWithSockets,
        SerializesModels;

    /**
     * @var Falta_profesor
     */
    public $fichaje;

    /**
     * @param Falta_profesor $fichaje
     */
    public function __construct(Falta_profesor $fichaje)
    {
        $this->fichaje = $fichaje;
    }

}
