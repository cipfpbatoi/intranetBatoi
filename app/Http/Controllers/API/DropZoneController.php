<?php

namespace Intranet\Http\Controllers\API;

use Illuminate\Http\Request;
use Intranet\Entities\Adjunto;
use Intranet\Entities\Profesor;
use Intranet\Http\Controllers\Controller;
use Intranet\Http\Controllers\API\ApiBaseController;
use Intranet\Services\AttachedFileService;

class DropZoneController extends ApiBaseController
{
    public function getAttached($modelo,$id){
        $files = Adjunto::findByModel($modelo,$id)->get();
        $data = [];
        foreach ($files as $key => $attached){
            $data[$key]['name'] = $attached->name;
            $data[$key]['extension'] = $attached->extension;
            $data[$key]['size'] = $attached->size;
            $data[$key]['accepted'] = true;
        }
        return $this->sendResponse($data, 'OK');
    }

    public function removeAttached($modelo,$id,$file){
        $adjunto = Adjunto::findByName($modelo,$id,$file)->first();
        if ($adjunto) {
            if (AttachedFileService::delete($adjunto)){
                return $this->sendResponse([],'OK');
            }
            return $this->sendError("No s'ha pogut esborrar");
        }
        return $this->sendError("No s'ha trobat");
    }

    public function attachFile(Request $request){
        $user = $this->ApiUser($request);
        if (AttachedFileService::save($request->file('file'),$request->modelo,
            $request->id,$user->dni)) {
            return $this->sendResponse(['data'=>'OK'],'OK');
        } else {
            return $this->sendError("No s'ha pogut completar l'operacio");
        }
    }

}
