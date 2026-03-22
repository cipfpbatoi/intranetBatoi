<?php

namespace Intranet\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Intranet\Entities\Reunion;

/**
 * Event de reunio creada.
 */
class ReunionCreated
{

    use Dispatchable,
        InteractsWithSockets,
        SerializesModels;

    /**
     * @var Reunion
     */
    public $reunion;

    /**
     * @param Reunion $reunion
     */
    public function __construct(Reunion $reunion)
    {
        $this->reunion = $reunion;
    }

}
