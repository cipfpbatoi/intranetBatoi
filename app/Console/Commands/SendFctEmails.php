<?php

namespace Intranet\Console\Commands;

use ErrorException;
use Exception;
use Illuminate\Console\Command;
use Intranet\Entities\AlumnoFctAval;
use Intranet\Mail\CertificatAlumneFct;
use Intranet\Mail\CertificatInstructorFct;
use Illuminate\Support\Facades\Mail;
use Intranet\Mail\AvalFct;
use Intranet\Entities\Profesor;
use Styde\Html\Facades\Alert;
use Swift_RfcComplianceException;
use Swift_TransportException;

class SendFctEmails extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fct:Daily';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Email valoració FCT';


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

             $alumnosPendientes = AlumnoFctAval::pendienteNotificar()->get();

            foreach ($alumnosPendientes as $alumno) {
                $fct = $alumno->Fct;

                try {
                    if ($fct->Encarregat == null) {
                        throw new ErrorException('No hi ha tutor assignat a la FCT');
                    }
                    Mail::to($alumno->Alumno->email, $alumno->Alumno->fullName)->send(new CertificatAlumneFct($alumno));
                    avisa($fct->Encarregat->dni,
                        'El correu amb el certificat de FCT de ' . $alumno->Alumno->fullName . " ha estat enviat a l'adreça " . $alumno->Alumno->email,
                        '#', 'Servidor de correu');
                    $alumno->correoAlumno = 1;
                    $alumno->save();
                } catch (Exception  $e) {
                    $mensaje = "Error : Enviant certificats a l'alumne:  ".$e->getMessage().".".
                        $alumno->Alumno->fullName.' al email '.
                        $alumno->Alumno->email;
                    avisa(config('avisos.errores'), $mensaje, '#', 'Servidor de correu');
                    if ($fct->Encarregat != null) {
                        avisa($fct->Encarregat->dni, $mensaje, '#', 'Servidor de correu');
                    }

                }

                if ($fct->correoInstructor == 0 && isset($fct->Instructor->email)) {
                    try {
                        Mail::to($fct->Instructor->email, $fct->Instructor->nombre)
                            ->cc($fct->Tutor->email, $fct->Tutor->fullName)
                            ->send(new AvalFct($fct, 'instructor'));
                        Mail::to($fct->Instructor->email, $fct->Instructor->nombre)
                            ->send(new CertificatInstructorFct($fct));
                        avisa($fct->tutor->dni,
                            'El correu amb el certificat de FCT de ' . $fct->Instructor->nombre . " ha estat enviat a l'adreça " . $fct->Instructor->email,
                            '#', 'Servidor de correu');
                        $fct->correoInstructor = 1;
                        $fct->save();
                    } catch (\Exception $e) {
                        $mensaje = 'Error : Enviant certificats al Instructor: '.
                            $fct->Instructor->nombre.' al email '.
                            $fct->Instructor->email.
                            $fct->Encarregat. $e->getMessage();
                        avisa(config('avisos.errores'), $mensaje, '#', 'Servidor de correu');
                        if ($fct->Encarregat != null) {
                            avisa($fct->Encarregat->dni, $mensaje, '#', 'Servidor de correu');
                        }

                    }
                }
            }
        }

}
