<?php

namespace Intranet\Http\Controllers\API;

use Intranet\Entities\Alumno;
use Intranet\Entities\AlumnoReunion;



class AlumnoReunionController extends ApiBaseController
{

    const NOPROMOCIONA = 3;

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
       $curso_actual = $grupo->curso;
       if ($capacitat == self::NOPROMOCIONA) {
           $promociona = false;
           $curso = $curso_actual;
       } else {
           $promociona = true;
           $curso = ($grupo->isSemi)?'fct':2;
       }
       return $this->sendResponse(compact('nia','nombre','apellidos','email','fecha_nac','ciclo','promociona','curso','curso_actual'),'OK');
    }

    public function getDadesMatricula($token){
        $aR = AlumnoReunion::where('token',$token)->first();
        if (!$aR) {
            return $this->sendError('Token no vàlid');
        }
        return $this->getDades($aR->idAlumno);
    }


}
