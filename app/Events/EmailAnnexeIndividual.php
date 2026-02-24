<?php

namespace Intranet\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event d'enviament d'annex individual.
 */
class EmailAnnexeIndividual
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var string
     */
    public $signatura;

    /**
     * @param string $signatura
     */
    public function __construct($signatura)
    {
        $this->signatura = $signatura;
    }
}
