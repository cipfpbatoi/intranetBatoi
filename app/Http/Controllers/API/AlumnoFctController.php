<?php

namespace Intranet\Http\Controllers\API;

use Intranet\Entities\AlumnoFct;
use Intranet\Entities\Grupo;
use Illuminate\Http\Request;
use Intranet\Http\Resources\SelectAlumnoFctResource;


class AlumnoFctController extends ApiBaseController
{

    protected $model = 'AlumnoFct';

    public function indice($grupo)
    {
        $grup = Grupo::findOrFail($grupo);
        $data = AlumnoFct::Grupo($grup)->esFct()->get();
        foreach ($data as $dato) {
            if ($dato->Fct->idColaboracion) {
                $dato->centro = $dato->Fct->Colaboracion->Centro->nombre;
                $dato->nombre = $dato->Alumno->fullName;
            }
        }
        return $this->sendResponse($data, 'OK');
    }

    public function update(Request $request, $id)
    {
        $registro = AlumnoFct::findOrFail($id);
        $registro->pg0301 = $request->pg0301==='true'?1:0;
        $registro->save();
        return $this->sendResponse(['updated' => true], 'OK');
    }



}
