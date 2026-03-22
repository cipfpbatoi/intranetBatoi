<?php

namespace Intranet\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Intranet\Entities\Fct;

/**
 * Event de FCT creada.
 */
class FctCreated
{

    use Dispatchable,
        InteractsWithSockets,
        SerializesModels;

    /**
     * @var Fct
     */
    public $fct;

    /**
     * @param Fct $fct
     */
    public function __construct(Fct $fct)
    {
        $this->fct = $fct;
    }

}
