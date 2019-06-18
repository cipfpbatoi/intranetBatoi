<?php

namespace Intranet\Console\Commands;

use Illuminate\Console\Command;
use Intranet\Entities\Profesor;
use Intranet\Entities\AlumnoFctAval;
use Mail;
use Intranet\Mail\AvalFct;
use Swift_RfcComplianceException;

class SendFctEmails extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fct:Weekly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Email valoració FCT';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $alumnosCalificados = hazArray(AlumnoFctAval::calificados()->get(),'idAlumno');
        $alumnosPendientes = AlumnoFctAval::pendienteNotificar($alumnosCalificados)->get();


        foreach ($alumnosPendientes as $alumno) {
            try {
                Mail::to($alumno->Alumno->email, 'Intranet Batoi')->send(new AvalFct($alumno, 'alumno'));
                $alumno->correoAlumno = 1;
                $alumno->save();
                $fct = $alumno->Fct;
            } catch (Swift_RfcComplianceException $e){

            }
            if ($fct->correoInstructor == 0){
                try {
                    Mail::to($fct->Instructor->email, 'Intranet Batoi')->send(new AvalFct($fct, 'instructor'));
                    $fct->correoInstructor = 1;
                    $fct->save();
                } catch (Swift_RfcComplianceException $e){

                }
            }
        }
    }

}
