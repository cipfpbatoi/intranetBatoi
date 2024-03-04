<?php

namespace Intranet\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Intranet\Entities\AlumnoFct;
use Intranet\Entities\Signatura;
use Intranet\Events\EmailAnnexeIndividual;

class ActualitzarBaseDeDadesDespresEmail
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(EmailAnnexeIndividual $event): void
    {
        $signatura = $event->signatura;
        if ($signatura instanceof AlumnoFct){
            $this->updateAlumnoFct($signatura);
            return;
        }
        if ($signatura instanceof Signatura){
            $this->update($signatura);
            return;
        }
        if (is_iterable($signatura)){
            foreach ($signatura as $sig) {
                if ($sig instanceof Signatura) {
                    $this->update($sig);
                    return;
                }
                if ($sig instanceof AlumnoFct){
                    $this->updateAlumnoFct($sig);
                    return;
                }
            }
        }
    }

    private function updateAlumnoFct($alumnoFct)
    {
        foreach ($alumnoFct->Signatures as $s) {
            $this->update($s);
        }
    }

    private function update($signatura)
    {
        if ($signatura->sendTo < 2) {
            $signatura->sendTo += 2;
            $signatura->save();
        }
    }
}
