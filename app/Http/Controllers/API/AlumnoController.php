<?php

namespace Intranet\Http\Controllers\API;

use Intranet\Entities\Alumno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AlumnoController extends ApiBaseController
{

    public function putImage(Request $request,$id)
    {
        if ($request->hasFile('foto')) {
            $validator = Validator::make($request->all(), [
                'foto' => 'required|image:jpeg,png,jpg,gif,svg|max:2048'
            ]);
            if ($validator->fails()) {
                return $this->sendError('Format foto no valida');
            }
            if ($alumno = Alumno::where('dni', $id)->first()){
                $alumno->foto = $request->file('foto')->store('fotos', 'public');
                $alumno->save();
                return $this->sendResponse("$id", 'OK');
            }
            return $this->sendError('No existeix alumne '.$id);
        }
        return $this->sendError('No hi ha foto');
    }

    public function putDades(Request $request,$id)
    {
        $validator = Validator::make($request->all(), [
            'imageRightAccept' => 'required|boolean',
            'outOfSchoolActivityAccept' => 'required|boolean'
        ]);
        if ($validator->fails()) {
            return $this->sendError('Format dades no valides');
        }
        if ($alumno = Alumno::where('dni', $id)->first()){
            $alumno->imageRightAccept = $request->imageRightAccept;
            $alumno->outOfSchoolActivityAccept = $request->outOfSchoolActivityAccept;
            $alumno->save();
            return $this->sendResponse("$id", 'OK');
        }
        return $this->sendError('No existeix alumne '.$id);
    }
}
