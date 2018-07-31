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
        $todos = Profesor::activo()->orderBy('departamento')->get();
        foreach ($todos as $uno){
            $uno->nombre = $uno->FullName;
            $uno->departamento = $uno->Ldepartamento;
            $uno->email = $uno->Entrada;
            $uno->emailItaca = $uno->Salida;
            $uno->codigo_postal = $uno->Horario;
        }
        return $this->sendResponse($todos, 'OK');
    }

}
