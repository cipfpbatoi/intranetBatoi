<?php

namespace Intranet\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Intranet\Entities\Falta_profesor;
use Intranet\Events\FichaCreated;

class VeepCreated
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
     * @param  Login  $event
     * @return void
     */
    public function handle(FichaCreated $event)
    {
        if (in_array($event->ficha->idProfesor,config('auxiliares.veep'))){
            $event->ficha->entrada = restarHoras("00:35:55",$event->ficha->entrada );
            $event->ficha->save();
        }
    }

}
