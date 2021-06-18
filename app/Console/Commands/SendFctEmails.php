<?php

namespace Intranet\Console\Commands;

use Illuminate\Console\Command;
use Intranet\Entities\AlumnoFctAval;
use Intranet\Mail\CertificatAlumneFct;
use Intranet\Mail\CertificatInstructorFct;
use Illuminate\Support\Facades\Mail;
use Intranet\Mail\AvalFct;
use Swift_RfcComplianceException;

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
        if (config('curso.enquestesAutomatiques')){
            $alumnosAprobados = hazArray(AlumnoFctAval::aprobados()->get(),'idAlumno');
            $alumnosPendientes = AlumnoFctAval::pendienteNotificar($alumnosAprobados)->get();


            foreach ($alumnosPendientes as $alumno) {
                $fct = $alumno->Fct;
                try {
                    Mail::to($alumno->Alumno->email, '')
                        ->send(new CertificatAlumneFct($alumno));
                    $alumno->correoAlumno = 1;
                    $alumno->save();

                } catch (Swift_RfcComplianceException $e){

                }

                if ($fct->correoInstructor == 0 && isset($fct->Instructor->email)){
                    try {
                        Mail::to($fct->Instructor->email, 'Intranet Batoi')->send(new AvalFct($fct, 'instructor'));
                        Mail::to($fct->Instructor->email, 'Secretaria CIPFP Batoi')
                            ->send(new CertificatInstructorFct($fct));

                        $fct->correoInstructor = 1;
                        $fct->save();
                    } catch (Swift_RfcComplianceException $e){

                    }
                }
            }
        }
    }


}
