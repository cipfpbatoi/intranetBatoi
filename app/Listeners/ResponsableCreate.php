<?php

namespace Intranet\Listeners;

use Intranet\Application\Grupo\GrupoService;
use Intranet\Events\ActividadCreated;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Intranet\Entities\Actividad;
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
        if (authUser()) {
            $actividad = Actividad::findOrFail($event->actividad->id);
            $actividad->profesores()->attach(authUser()->dni, ['coordinador' => 1]);
            $grupo = app(GrupoService::class)->largestByTutor(AuthUser()->dni);
            if ($grupo) {
                $actividad->grupos()->attach($grupo->codigo);
            }
        }
    }

}
