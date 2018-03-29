<?php

namespace Intranet\Http\Controllers\API;

use Intranet\Entities\Profesor;
use Intranet\Entities\Falta_profesor;
use Intranet\Entities\Horario;
use Illuminate\Http\Request;

class ProfesorController extends ApiBaseController
{

    protected $model = 'Profesor';

    public function rol($dni)
    {
        $data = Profesor::select('rol')
                ->where('dni', $dni)
                ->first();
        return $this->sendResponse($data, 'OK');
    }
    
    public function ficha(){
        $todos = Profesor::select('dni','nombre','apellido1','apellido2','departamento','email','emailItaca','codigo_postal')->activo()->orderBy('departamento')->get();
        foreach ($todos as $uno){
            $uno->nombre = $uno->FullName;
            $uno->departamento = $uno->Departamento->literal;
            $ficha = Falta_profesor::Hoy($uno->dni)->last();
            if ($ficha){
                $uno->email = $ficha->entrada;
                $uno->emailItaca = $ficha->salida;
            }
            else{
                $uno->email = '';
                $uno->emailItaca = '';
            }
            $horario = Horario::Primera($uno->dni)->orderBy('desde')->get();
            if (isset($horario->first()->desde)){
                $uno->codigo_postal = $horario->first()->desde." - ".$horario->last()->hasta;
            }
            else $uno->codigo_postal = '';
        }
        return $this->sendResponse($todos, 'OK');
    }

}
