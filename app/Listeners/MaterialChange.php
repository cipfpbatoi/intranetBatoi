<?php

namespace Intranet\Listeners;

use Intranet\Events\IncidenciaSaved;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Auth;
use Intranet\Entities\Material;

class MaterialChange
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
     * @param  IncidenciaCreated  $event
     * @return void
     */
    public function handle(IncidenciaSaved $event)
    {
        if (AuthUser()) {
            $material = Material::find($event->incidencia->material);
            if ($material) {
                $material->estado = ($event->incidencia->estado == 1 || $event->incidencia->estado == 2) ? '2' : '1';
                $material->save();
            }
        }
    }

}
