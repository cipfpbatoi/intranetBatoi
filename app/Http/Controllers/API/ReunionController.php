<?php

namespace Intranet\Http\Controllers\API;

use Intranet\Entities\Reunion;
use Illuminate\Http\Request;

class ReunionController extends ApiResourceController
{

    protected $model = 'Reunion';
    
    protected function putAlumno($idReunion,$idAlumno, Request $request){
        $reunion = Reunion::findOrFail($idReunion);
        if ($reunion){
            $reunion->alumnos()->updateExistingPivot($idAlumno,['capacitats'=> $request->capacitats]);
            return $this->sendResponse($request->capacitats, 'OK');
        }

        return $this->sendError('Reunió no trobada', 404);
    }

}
