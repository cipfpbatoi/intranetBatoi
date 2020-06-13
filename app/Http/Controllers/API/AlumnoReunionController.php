<?php

namespace Intranet\Http\Controllers\API;

use Intranet\Entities\Alumno;
use Intranet\Entities\AlumnoFct;
use Intranet\Entities\AlumnoReunion;
use Intranet\Entities\Grupo;
use Illuminate\Http\Request;


class AlumnoReunionController extends ApiBaseController
{

    protected $model = 'AlumnoReunion';

    private function getDades($nia){
       $alumno = Alumno::find($nia);
       $capacitat = AlumnoReunion::where('idAlumno',$nia)->min('capacitats');
       $fecha_nac = $alumno->fecha_nac;
       $nombre = $alumno->nombre;
       $apellidos = $alumno->apellido1.' '.$alumno->apellido2;
       $email = $alumno->email ;
       $nia = $alumno->nia;
       $grupo = $alumno->Grupo->first();
       $ciclo = $grupo->idCiclo;
       if ($capacitat == 3) {
           $promociona = false;
           $curso = $grupo->curso;
       } else {
           $promociona = true;
           $curso = ($grupo->curso == 2)?'fct':2;
       }
       return $this->sendResponse(compact('nia','nombre','apellidos','email','fecha_nac','ciclo','promociona','curso'),'OK');
    }

    private function checkDades($token){
        $aR = AlumnoReunion::where('token',$token)->first();
        if (!$aR) return $this->sendError('Token no vàlid');
        return $this->getDades($aR->idAlumno);
    }
    public function getDadesMatricula($convocatoria,$token){
        if ($convocatoria != config('curso.convocatoria')) return $this->sendError('La convocatòria no està oberta');
        return $this->checkDades($token);
    }

}
