<?php

namespace Intranet\Console\Commands;

use Illuminate\Console\Command;
use Intranet\Entities\AlumnoReunion;
use Intranet\Mail\AvalAlumne;
use Intranet\Mail\extraOrdinariaAlumne;
use Illuminate\Support\Facades\Mail;
use Swift_RfcComplianceException;
use Illuminate\Support\Str;

class SendAvaluacioEmails extends Command
{
    const PROMOCIONA = 2;
    const NOPROMOCIONA = 3;
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

    private function sendOrdinaria($aR){
        try {
            $aR->sent = 1;
            if ($aR->Reunion->grupoClase->turno == 'S'){
                Mail::to($aR->Alumno->email,'Secretaria CIPFP Batoi')
                    ->send(new AvalAlumne($aR,'pdf.reunion.informe.semi'));
            }
            else {
                $aR->token = $this->generaToken(60);
                Mail::to($aR->Alumno->email,'Secretaria CIPFP Batoi')
                    ->send(new AvalAlumne($aR,'pdf.reunion.informe.individual'));
            }
            $aR->save();
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

    private function deleteToken($aO)
    {
        $aO->token = null;
        $aO->save();
    }

    private function sendExtraOrdinaria($aR){
        $aO = AlumnoReunion::with('Reunion')
            ->where('sent',1)
            ->where('idAlumno',$aR->idAlumno)
            ->first();
        try {
            $aR->sent = 1;
            $grupo = $aR->Reunion->grupoClase;

            if ($aO && $grupo->isSemi && $aR->capacitats == self::NOPROMOCIONA){
                $this->deleteToken($aO);
            } else {
                $capacitats = ($grupo->isSemi || $grupo->curso == '1' )?self::PROMOCIONA:self::NOPROMOCIONA;
                if ($aR->capacitats == $capacitats){
                    $informe = ($grupo->isSemi)?'semi':$grupo->curso;
                    if (!$aO){
                        $aR->token = $this->generaToken(60);
                    } else {
                        $aR->token = $aO->token;
                    }

                    Mail::to($aR->Alumno->email,'Secretaria CIPFP Batoi')
                        ->send(new extraOrdinariaAlumne(
                            $aR,'email.extra.'.$informe));
                }
                avisa($aR->Reunion->idProfesor,
                    'Missatge Avaluació Alumne '.$aR->Alumno->fullName. ' enviat a '.$aR->Alumno->email,
                    '#','Servidor de correu');
            }
            $aR->save();
        }
        catch (Swift_RfcComplianceException $e){
            avisa($aR->Reunion->idProfesor,
                'Error : Enviant missatge Avaluació Alumne '.$aR->Alumno->fullName. ' a '.$aR->Alumno->email,
                '#','Servidor de correu');
        }
    }
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        foreach ( AlumnoReunion::with('Reunion')->where('sent',0)->get() as $aR)
        {
            if ($aR->Reunion->extraOrdinaria){
                $this->sendExtraOrdinaria($aR);
            }
            else {
                $this->sendOrdinaria($aR);
            }

        }

    }


}
