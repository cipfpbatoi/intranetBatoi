<?php

namespace Intranet\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Intranet\Entities\AlumnoFct;

/**
 * Event de baixa d'alumne en FCT.
 */
class FctAlDeleted
{

    use Dispatchable,
        InteractsWithSockets,
        SerializesModels;

    /**
     * @var mixed
     */
    public $fct;

    /**
     * @param AlumnoFct $fctAl
     */
    public function __construct(AlumnoFct $fctAl)
    {
        $this->fct = $fctAl->Fct;
    }

}
