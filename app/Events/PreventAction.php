<?php

namespace Intranet\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Database\Eloquent\Model;

class PreventAction
{

    use Dispatchable,
        InteractsWithSockets,
        SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public $owner;
    public $autorizados;

    private function creador($model)
    {
        if (isset($model->dni)) {
            return $model->dni;
        }
        if (isset($model->idProfesor)) {
            return $model->idProfesor;
        }
        if (method_exists($model, 'Creador')) {
            return $model->Creador();
        }
    }

    public function __construct(Model $model)
    {
        $this->owner = $this->creador($model);
        switch (substr(get_class($model), 18)) {
            case 'Incidencia' :
                $this->autorizados = [config('roles.rol.direccion'), config('roles.rol.mantenimiento')];break;
            case 'TipoIncidencia' :
                $this->autorizados = [config('roles.rol.direccion'), config('roles.rol.mantenimiento')];break;
            case 'Programacion' :
                $this->autorizados = [ config('roles.rol.jefe_dpto')];break;
            default : $this->autorizados = [config('roles.rol.direccion')]; break;
        }
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
