<?php

namespace Intranet\Http\Controllers\API;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Intranet\Entities\Alumno;
use Intranet\Entities\AlumnoReunion;
use Illuminate\Http\Request;
use Intranet\Mail\MatriculaAlumne;

class AlumnoReunionController extends ApiBaseController
{

    const NOPROMOCIONA = 3;

    protected $model = 'AlumnoReunion';

    private function getDades($nia)
    {
       $alumno = Alumno::find($nia);
       $capacitat = AlumnoReunion::where('idAlumno', $nia)->min('capacitats');
       $grupo = AlumnoReunion::where('idAlumno', $nia)->first()->Reunion->grupoClase;
       $fecha_nac = $alumno->fecha_nac;
       $nombre = $alumno->nombre;
       $apellidos = $alumno->apellido1.' '.$alumno->apellido2;
       $email = $alumno->email ;
       $nia = $alumno->nia;
       $telef1 = $alumno->telef1;
       $telef2 = $alumno->telef2;
       $ciclo = $grupo->idCiclo;
       $dni = $alumno->dni;
       $curso_actual = $grupo->curso;
       $turno = $grupo->torn;
       if ($capacitat == self::NOPROMOCIONA) {
           $promociona = false;
           $curso = $curso_actual;
       } else {
           $promociona = true;
           $curso = ($grupo->isSemi)?'fct':2;
       }

       return $this->sendResponse(compact('nia','dni','nombre','apellidos','email','telef1','telef2','fecha_nac','ciclo','promociona','curso','curso_actual','turno'),'OK');
    }

    public function getDadesMatricula($token)
    {
        $aR = AlumnoReunion::where('token', $token)->first();
        if (!$aR) {
            return $this->sendError('Token no vàlid');
        }
        return $this->getDades($aR->idAlumno);
    }

    private function generaToken()
    {
        return Str::random(60);
    }

    public function sendMatricula(Request $request)
    {

        $alumne = Alumno::where('dni', $request->dni)->first();
        if (!$alumne) {
            return $this->sendError('DNI no vàlid');
        }

        $aR = AlumnoReunion::where('idAlumno', $alumne->nia)->first();

        if (!$aR) {
            return $this->sendError("Eixe alumne no te matricules pendents");
        } else {
            if (is_null($aR->token)) {
                $aR->token  = $this->generaToken();
                $aR->save();
            }
            try {
                Mail::to($request->email, 'Secretaria CIPFP Batoi')
                    ->send(new MatriculaAlumne(
                        $aR, config('variables.fitxerMatricula'), $request->convocatoria));
                return $this->sendResponse('OK', 'Email enviat');
            } catch (\Exception $e) {
                return $this->sendError('Error enviant email');
            }
        }
    }



    public function getTestMatricula($token)
    {
        if ($token == '2Kpd5xIfNYfx3U7aTaRWPQZtmF9LFlP6dXR07DB88DdL28ZMfWXsYKWAC0TV') {
            $alumno = Alumno::find('10001551');
            $fecha_nac = $alumno->fecha_nac;
            $nombre = $alumno->nombre;
            $apellidos = $alumno->apellido1.' '.$alumno->apellido2;
            $email = $alumno->email ;
            $nia = $alumno->nia;
            $telef1 = $alumno->telef1;
            $telef2 = $alumno->telef2;
            $ciclo = '51';
            $dni = $alumno->dni;
            $curso_actual = 2;
            $promociona = false;
            $curso = $curso_actual;
            $turno = 'M';
            return $this->sendResponse(compact('nia','dni','nombre','apellidos','email','telef1','telef2','fecha_nac','ciclo','promociona','curso','curso_actual','turno'),'OK');
        }
        if ($token == '1Kpd5xIfNYfx3U7aTaRWPQZtmF9LFlP6dXR07DB88DdL28ZMfWXsYKWAC0TV') {
            $alumno = Alumno::find('10659775');
            $fecha_nac = $alumno->fecha_nac;
            $nombre = $alumno->nombre;
            $apellidos = $alumno->apellido1.' '.$alumno->apellido2;
            $email = $alumno->email ;
            $nia = $alumno->nia;
            $telef1 = $alumno->telef1;
            $telef2 = $alumno->telef2;
            $ciclo = '51';
            $dni = $alumno->dni;
            $curso_actual = 1;
            $promociona = true;
            $curso = 2;
            $turno = 'V';
            return $this->sendResponse(compact('nia','dni','nombre','apellidos','email','telef1','telef2','fecha_nac','ciclo','promociona','curso','curso_actual','turno'),'OK');
        }
        if ($token == '3Kpd5xIfNYfx3U7aTaRWPQZtmF9LFlP6dXR07DB88DdL28ZMfWXsYKWAC0TV') {
            $alumno = Alumno::find('10810396');
            $fecha_nac = $alumno->fecha_nac;
            $nombre = $alumno->nombre;
            $apellidos = $alumno->apellido1.' '.$alumno->apellido2;
            $email = $alumno->email ;
            $nia = $alumno->nia;
            $telef1 = $alumno->telef1;
            $telef2 = $alumno->telef2;
            $ciclo = '51';
            $dni = $alumno->dni;
            $curso_actual = 1;
            $promociona = true;
            $curso = 2;
            $turno = 'M';
            return $this->sendResponse(compact('nia','dni','nombre','apellidos','email','telef1','telef2','fecha_nac','ciclo','promociona','curso','curso_actual','turno'),'OK');
        }
        if ($token == '4Kpd5xIfNYfx3U7aTaRWPQZtmF9LFlP6dXR07DB88DdL28ZMfWXsYKWAC0TV') {
            $alumno = Alumno::find('10677040');
            $fecha_nac = $alumno->fecha_nac;
            $nombre = $alumno->nombre;
            $apellidos = $alumno->apellido1.' '.$alumno->apellido2;
            $email = $alumno->email ;
            $nia = $alumno->nia;
            $telef1 = $alumno->telef1;
            $telef2 = $alumno->telef2;
            $ciclo = '51';
            $dni = $alumno->dni;
            $curso_actual = 2;
            $promociona = true;
            $curso = 'fct';
            $turno = 'S';
            return $this->sendResponse(compact('nia','dni','nombre','apellidos','email','telef1','telef2','fecha_nac','ciclo','promociona','curso','curso_actual','turno'),'OK');
        }
        return $this->sendError('Token no vàlid');
    }


}
