<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Seeder;
use Intranet\Mail\MatriculaAlumne;
use Styde\Html\Facades\Alert;
use Intranet\Entities\AlumnoReunion;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

/**
 * Class ImportController
 * @package Intranet\Http\Controllers
 */
class SendAvaluacioEmailController extends Seeder
{

    const PROMOCIONA = 1;
    const NOPROMOCIONA = 3;

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('seeder.sendAvaluacio');
    }


    private function generaToken()
    {
        return Str::random(60);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function store(Request $request)
    {
        $aR = AlumnoReunion::where('idAlumno',$request->nia)->get()->last();
        if (!$aR){
            Alert::danger('Eixe Alumne no ha estat avaluat');
        }
        else {
            $this->sendMatricula($aR);
        }
        return back();

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
            $token = true;
            if (!$aR->sent) {
                if ($token = $this->obtenToken($aR)){
                    $aR->sent = 1;
                    $aR->token = $token;
                    $aR->save();
                }
            }
            if ($token) {
                Mail::to($aR->Alumno->email, 'Secretaria CIPFP Batoi')
                    ->send(new MatriculaAlumne(
                        $aR, 'email.matricula'));
                avisa($aR->Reunion->idProfesor,
                    'El correu per a la matrícula de  ' . $aR->Alumno->fullName . " ha estat enviat a l'adreça " . $aR->Alumno->email,
                    '#', 'Servidor de correu');
                Alert::info('Correu enviat');
            }
        }
        catch (Swift_RfcComplianceException $e){
            avisa($aR->Reunion->idProfesor,
                'Error : Enviant missatge Avaluació Alumne '.$aR->Alumno->fullName. ' a '.$aR->Alumno->email,
                '#','Servidor de correu');
        }
    }

}
