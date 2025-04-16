<?php

namespace Intranet\Listeners;

use Intranet\Events\FctAlDeleted;
use Intranet\Entities\Fct;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Intranet\Entities\Actividad;
use Intranet\Entities\Grupo;
use Illuminate\Support\Facades\Auth;

class FctDelete
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
    public function handle(FctAlDeleted $event)
    {
        if (authUser()) {
            $fct= Fct::findOrFail($event->fct->id);
            if ($fct->Alumnos->Count() == 0) {
                $fct->delete();
            }
        }
    }

}
