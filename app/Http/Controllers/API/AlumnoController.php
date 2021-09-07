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
                if ($alumno->foto){
                    return $this->sendError("L'alumne $id ja te foto");
                } else {
                    $alumno->foto = $request->file('foto')->store('fotos', 'public');
                    $alumno->save();
                    return $this->sendResponse("$id", 'OK');
                }
            }
            else {
                return $this->sendError('No existeix alumne '.$id);
            }
        } else {
            return $this->sendError('No hi ha foto');
        }
    }
}
