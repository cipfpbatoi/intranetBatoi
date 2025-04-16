<?php

namespace Intranet\Http\Controllers\API;

use Illuminate\Http\Request;
use Intranet\Http\Requests;
use Intranet\Http\Controllers\Controller;
use Intranet\Http\Controllers\API\ApiBaseController;
use Intranet\Entities\Grupo;

class GrupoController extends ApiBaseController
{

    protected $model = 'Grupo';

    public function list($id){

        $return = [];
        foreach (Grupo::find($id)->Alumnos->sortBy('nameFull') as $alumno){
            $return[] = ['id' => $alumno->id, 'texto' => $alumno->nameFull, 'marked' => 1];
        }
        return ['data' => $return];
    }

}
