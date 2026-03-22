<?php

namespace Intranet\Listeners;

use Intranet\Events\FctCreated;
use Intranet\Entities\Fct;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Intranet\Entities\Actividad;
use Intranet\Entities\Grupo;
use Illuminate\Support\Facades\Auth;

/**
 * Marca la col·laboració com a finalitzada en crear una FCT.
 */
class ColaboracionColabora
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
     * @param  FctCreated  $event
     * @return void
     */
    public function handle(FctCreated $event)
    {
        if (authUser()) {
            $fct= Fct::findOrFail($event->fct->id);
            if ($colaboracion = $fct->Colaboracion) {
                $colaboracion->estado = 2;
                $colaboracion->save();
            }
        }
    }

}
