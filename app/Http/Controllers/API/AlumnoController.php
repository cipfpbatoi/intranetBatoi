<?php

namespace Intranet\Http\Controllers\API;

use Intranet\Entities\Alumno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Intranet\Services\Media\ImageService;

class AlumnoController extends ApiResourceController
{

    public function putImage(Request $request,$id)
    {
        if ($request->hasFile('foto')) {
            $fitxer = $request->file('foto');
            if ($alumno = Alumno::where('dni', $id)->first()){
                if ($fitxer->isValid()) {
                    if ($alumno->foto) {
                        ImageService::updatePhotoCarnet($fitxer, storage_path('app/public/fotos/'.$alumno->foto));
                        return $this->sendResponse("$id", 'Modificació foto feta amb exit');
                    }
                    $nameFile = ImageService::newPhotoCarnet($fitxer, storage_path('app/public/fotos'));
                    $alumno->foto = $nameFile;
                    return $this->sendResponse("$id", 'Foto nova guardada amb exit');
                }
                return $this->sendError('Formato no valido');
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
            return $this->sendError('Format dades no valides'. $validator->errors());
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
