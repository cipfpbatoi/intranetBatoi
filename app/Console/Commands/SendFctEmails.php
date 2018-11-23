<?php

namespace Intranet\Console\Commands;

use Illuminate\Console\Command;
use Intranet\Entities\Profesor;
use Intranet\Entities\AlumnoFctAval;
use Mail;
use Intranet\Mail\AvalFct;

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
        $todos = AlumnoFctAval::pendienteNotificar()->get();
        foreach ($todos as $alumno) {
            Mail::to($alumno->Alumno->email, 'Intranet Batoi')->send(new AvalFct($alumno,'alumno'));
            $alumno->correoAlumno = 1;
            $alumno->save();
            $fct = $alumno->Fct;
            if ($fct->correoInstructor == 0){
                Mail::to($fct->Instructor->email, 'Intranet Batoi')->send(new AvalFct($fct,'instructor'));
                foreach ($fct->Tutores() as $tutor)
                    Mail::to($tutor->email, 'Intranet Batoi')->send(new AvalFct($fct,'tutor'));
                $fct->correoInstructor = 1 ;
                $fct->save();
            }
        }
    }

}
