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


    private function generaToken()
    {
        return Str::random(60);
    }

    private function obtenToken($aR)
    {
        $grupo = $aR->Reunion->grupoClase;
        if ($grupo->isSemi) {
            return ($aR->capacitats == self::PROMOCIONA)?$this->generaToken():false;
        }
        if ($grupo->curso == '1' || $aR->capacitats == self::NOPROMOCIONA) {
            return $this->generaToken();
        }
        return false;
    }
    private function sendMatricula($aR)
    {
        try {
            if ($token = $this->obtenToken($aR)) {
                $aR->sent = 1;
                $aR->token = $token;
                Mail::to($aR->Alumno->email, 'Secretaria CIPFP Batoi')
                    ->send(new MatriculaAlumne($aR, config('variables.fitxerMatricula')));
                $mensaje = 'El correu per a la matrícula de  ' .$aR->Alumno->fullName ;
                $mensaje .= " ha estat enviat a l'adreça " . $aR->Alumno->email;
                avisa($aR->Reunion->idProfesor, $mensaje, '#', 'Servidor de correu');
                $aR->save();
            }
        } catch (\Exception $e) {
            $mensaje = 'Error : Enviant missatge Avaluació Alumne '.$aR->Alumno->fullName. ' a '.$aR->Alumno->email;
            avisa('021652470V', $mensaje, '#', 'Servidor de correu');
        }
    }
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        foreach (AlumnoReunion::with('Reunion')->where('sent', 0)->get() as $aR) {
            $this->sendMatricula($aR);
        }
    }


}
