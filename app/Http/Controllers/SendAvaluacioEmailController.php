<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Illuminate\Database\Seeder;
use Styde\Html\Facades\Alert;
use Intranet\Entities\AlumnoReunion;
use Illuminate\Support\Str;
use Mail;
use Intranet\Mail\AvalAlumne;
use Intranet\Mail\extraOrdinariaAlumne;

/**
 * Class ImportController
 * @package Intranet\Http\Controllers
 */
class SendAvaluacioEmailController extends Seeder
{

    const PROMOCIONA = 2;
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
            if ($aR->Reunion->Extraordinaria){
                $this->sendExtraOrdinaria($aR);
            }
            else{
                $this->sendOrdinaria($aR);
            }
        }
        return back();

    }

    private function sendOrdinaria($aR){
        if (!$aR->token){
            $aR->sent = 1;
            $aR->token = $this->generaToken(60);
            $aR->save();
        }
        if ($aR->Reunion->grupoClase->turno == 'S'){
            Mail::to($aR->Alumno->email,'Secretaria CIPFP Batoi')
                ->send(new AvalAlumne($aR,'pdf.reunion.informe.semi'));
        }
        else {
            Mail::to($aR->Alumno->email,'Secretaria CIPFP Batoi')
                ->send(new AvalAlumne($aR,'pdf.reunion.informe.individual'));
        }
        avisa($aR->Reunion->idProfesor,
            'Missatge Avaluació Alumne '.$aR->Alumno->fullName. ' enviat a '.$aR->Alumno->email,
            '#','Servidor de correu');
        Alert::info('Correu processat');
    }

    private function sendExtraOrdinaria($aR){
        $token = $aR->token??AlumnoReunion::with('Reunion')
            ->where('sent',1)
            ->where('idAlumno',$aR->idAlumno)
            ->first()
            ->token??$this->generaToken();
        $aR->sent = 1;
        $grupo = $aR->Reunion->grupoClase;

        $capacitats = ($grupo->isSemi || $grupo->curso == '1' )?self::PROMOCIONA:self::NOPROMOCIONA;

        if ($aR->capacitats == $capacitats){
            $informe = ($grupo->isSemi)?'semi':$grupo->curso;

            $aR->token = $token;

            Mail::to($aR->Alumno->email,'Secretaria CIPFP Batoi')
                ->send(new extraOrdinariaAlumne(
                    $aR,'email.extra.'.$informe));
        }
        else {
            return $this->sendOrdinaria($aR);
        }
        avisa($aR->Reunion->idProfesor,
            'Missatge Avaluació Alumne '.$aR->Alumno->fullName. ' enviat a '.$aR->Alumno->email,
            '#','Servidor de correu');

        $aR->save();
        Alert::info('Correu processat');



    }



}
