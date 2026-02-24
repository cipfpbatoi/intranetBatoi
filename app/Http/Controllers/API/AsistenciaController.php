<?php

namespace Intranet\Http\Controllers\API;

use Illuminate\Http\Request;
use Intranet\Entities\Reunion;
use Intranet\Http\Requests;
use Intranet\Http\Controllers\Controller;

class AsistenciaController extends ApiResourceController
{

    protected $model = 'Asistencia';

    public function cambiar(Request $request)
    {
        $reunion = Reunion::findOrFail($request->idReunion);
        if ($reunion) {
            $reunion->profesores()->updateExistingPivot($request->idProfesor, ['asiste' => $request->asiste]);
            return $this->sendResponse(['updated' => true], $reunion);
        } else {
            return $this->sendResponse(['updated' => false], 'KO');
        }
    }

}
