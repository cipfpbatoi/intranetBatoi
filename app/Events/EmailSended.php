<?php

namespace Intranet\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class EmailSended
{

    use Dispatchable,
        InteractsWithSockets,
        SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $elemento;
    public $modelo;
    public $correo;
    
    private function getmodel($elemento)
    {
        $entero = get_class($elemento);
        $nspace = 'Intranet\Entities\\';
        return substr($entero, strlen($nspace), strlen($entero));
    }

    public function __construct($elemento,$correo)
    {
        $this->elemento = $elemento;
        $this->modelo = $this->getmodel($elemento);
        $this->correo = $correo;
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
