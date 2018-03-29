<?php

namespace Intranet\Listeners;

use Intranet\Events\GrupoCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Auth;
use Intranet\Entities\Miembro;

class CoordinadorCreate
{

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  GrupoCreated  $event
     * @return void
     */
    public function handle(GrupoCreated $event)
    {
        if (AuthUser()) {
            $a = new Miembro;
            $a->idProfesor = AuthUser()->dni;
            $a->idGrupoTrabajo = $event->grupo->id;
            $a->coordinador = true;
            $a->save();
        }
    }

}
