<?php

namespace Intranet\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Intranet\Entities\Actividad;

class ActividadCreated
{

    use Dispatchable,
        InteractsWithSockets,
        SerializesModels;

    public $actividad;

    public function __construct(Actividad $actividad)
    {
        $this->actividad = $actividad;
     }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }

}
