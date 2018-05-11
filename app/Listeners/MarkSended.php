<?php

namespace Intranet\Listeners;

use Intranet\Events\EmailSended;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Intranet\Entities\Instructor;
use Intranet\Entities\Alumno;


class MarkSended
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
     * @param  ReunionCreated  $event
     * @return void
     */
    public function handle(EmailSended $event)
    {
        if ($event->modelo == 'Fct'){
            if (Instructor::where('email',$event->correo)->get())
                    $event->elemento->correoInstructor = 1;
            if (Alumno::where('email',$event->correo)->get())
                    $event->elemento->correoAlumno = 1;
            $event->elemento->correoAlumno = 1;
            $event->elemento->save();
        }
    }

}
