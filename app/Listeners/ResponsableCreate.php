<?php

namespace Intranet\Listeners;

use Intranet\Events\ActividadCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Intranet\Entities\Actividad;
use Intranet\Entities\Grupo;
use Illuminate\Support\Facades\Auth;

class ResponsableCreate
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
     * @param  ActividadCreated  $event
     * @return void
     */
    public function handle(ActividadCreated $event)
    {
        if (AuthUser()) {
            $actividad = Actividad::findOrFail($event->actividad->id);
            $actividad->profesores()->attach(AuthUser()->dni,['coordinador' => 1]);
            $grupo = Grupo::QTutor()->first();
            if ($grupo) $actividad->grupos()->attach($grupo->codigo);
        }
    }

}
