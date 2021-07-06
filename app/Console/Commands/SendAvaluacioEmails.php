<?php

namespace Intranet\Console\Commands;

use Illuminate\Console\Command;
use Intranet\Entities\AlumnoReunion;
use Illuminate\Support\Facades\Mail;
use Intranet\Mail\MatriculaAlumne;
use Swift_RfcComplianceException;
use Illuminate\Support\Str;

class SendAvaluacioEmails extends Command
{
    const NOPROMOCIONA = 3;
    const PROMOCIONA = 1;
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
    protected $description = 'Email Matricula Alumnat';

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

    private function obtenToken($aR)
    {
        $grupo = $aR->Reunion->grupoClase;
        if ($grupo->isSemi){
            if ($aR->capacitats == self::PROMOCIONA) {
                return $this->generaToken();
            } else {
                return false;
            }
        }
        if ($grupo->curso == '1') return $this->generaToken();
        if ($aR->capacitats == self::NOPROMOCIONA)  return $this->generaToken();

        return false;
    }
    private function sendMatricula($aR){
        try {
            if ($token = $this->obtenToken($aR)) {
                $aR->sent = 1;
                $aR->token = $token;
                Mail::to($aR->Alumno->email, 'Secretaria CIPFP Batoi')
                    ->send(new MatriculaAlumne(
                        $aR, 'email.matricula'));
                avisa($aR->Reunion->idProfesor,
                    'Missatge Avaluació Alumne ' . $aR->Alumno->fullName . ' enviat a ' . $aR->Alumno->email,
                    '#', 'Servidor de correu');
                $aR->save();
            }
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
            $this->sendMatricula($aR);
        }

    }


}
