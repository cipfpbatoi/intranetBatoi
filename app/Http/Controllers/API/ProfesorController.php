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

    public function getRol($rol)
    {
        $all = Profesor::Activo()->get();
        $data = [];
        foreach ($all as $profesor){
            if ($profesor->rol % $rol == 0){
                $data[$profesor->dni]=$profesor->fullName;
            }
        }
        return $this->sendResponse($data, 'OK');
    }
    
}
