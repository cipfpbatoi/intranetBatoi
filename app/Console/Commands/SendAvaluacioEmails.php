<?php

namespace Intranet\Console\Commands;

use Illuminate\Console\Command;
use Intranet\Entities\AlumnoReuniones;
use Intranet\Mail\AvalAlumne;
use Mail;
use Swift_RfcComplianceException;
use Illuminate\Support\Str;

class SendAvaluacioEmails extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'avaluacio:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Email Avaluació Alumnat';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }


    private function generaToken()
    {
        return Str::random(60);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        foreach ( AlumnoReuniones::where('sent',0)->get() as $aR)
        {
            try {
                $aR->sent = 1;
                $aR->token = $this->generaToken(60);
                $aR->save();
                if ($aR->Reunion->grupoClase->turno == 'S'){
                    Mail::to($aR->Alumno->email,'Secretaria CIPFP Batoi')
                        ->send(new AvalAlumne($aR,'pdf.reunion.informeSemi'));
                }
                else {
                    Mail::to($aR->Alumno->email,'Secretaria CIPFP Batoi')
                        ->send(new AvalAlumne($aR,'pdf.reunion.informeIndividual'));
                }

                avisa($aR->Reunion->idProfesor,
                    'Missatge Avaluació Alumne '.$aR->Alumno->fullName. ' enviat a '.$aR->Alumno->email,
                    '#','Servidor de correu');

            }
            catch (Swift_RfcComplianceException $e){
                avisa($aR->Reunion->idProfesor,
                    'Error : Enviant missatge Avaluació Alumne '.$aR->Alumno->fullName. ' a '.$aR->Alumno->email,
                    '#','Servidor de correu');
            }
        }

    }


}
