<?php

namespace Intranet\Services\Mail;

use Intranet\Entities\Alumno;
use Intranet\Entities\AlumnoFct;
use Intranet\Entities\Fct;
use Intranet\Entities\Instructor;
use Intranet\Entities\Signatura;

/**
 * Accions post-enviament per a correus.
 */
class EmailPostSendService
{
    /**
     * Actualitza l'estat d'enviament d'annexos individuals.
     *
     * @param mixed $signatura
     * @return void
     */
    public function handleAnnexeIndividual($signatura): void
    {
        if ($signatura instanceof AlumnoFct) {
            $this->updateAlumnoFct($signatura);
            return;
        }
        if ($signatura instanceof Signatura) {
            $this->updateSignatura($signatura);
            return;
        }
        if (is_iterable($signatura)) {
            foreach ($signatura as $sig) {
                if ($sig instanceof Signatura) {
                    $this->updateSignatura($sig);
                    return;
                }
                if ($sig instanceof AlumnoFct) {
                    $this->updateAlumnoFct($sig);
                    return;
                }
            }
        }
    }

    /**
     * Marca el correu enviat per a FCT.
     *
     * @param mixed $elemento
     * @param string $correo
     * @return void
     */
    public function markFctEmailSent($elemento, string $correo): void
    {
        if (!$elemento instanceof Fct) {
            return;
        }

        if (Instructor::where('email', $correo)->get()) {
            $elemento->correoInstructor = 1;
        }
        if (Alumno::where('email', $correo)->get()) {
            $elemento->correoAlumno = 1;
        }

        $elemento->save();
    }

    /**
     * @param AlumnoFct $alumnoFct
     * @return void
     */
    private function updateAlumnoFct(AlumnoFct $alumnoFct): void
    {
        foreach ($alumnoFct->Signatures as $signatura) {
            $this->updateSignatura($signatura);
        }
    }

    /**
     * @param Signatura $signatura
     * @return void
     */
    private function updateSignatura(Signatura $signatura): void
    {
        if ($signatura->sendTo < 2) {
            $signatura->sendTo += 2;
            $signatura->save();
        }
    }
}
