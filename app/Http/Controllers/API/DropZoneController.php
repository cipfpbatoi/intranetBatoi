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
    public function getAttached($modelo, $id)
    {
        $path = "$modelo/$id";
        $files = Adjunto::getByPath($path)->get();
        $data = [];
        foreach ($files as $key => $attached) {
            $data[$key]['name'] = $attached->name;
            $data[$key]['extension'] = $attached->extension;
            $data[$key]['size'] = $attached->size;
            $data[$key]['accepted'] = true;
            if (isset($attached->referencesTo)) {
                $data[$key]['referencesTo'] = $attached->referencesTo;
            }
        }
        return $this->sendResponse($data, 'OK');
    }

    public function removeAttached($modelo, $id, $file)
    {
        $user = apiAuthUser();
        $path = "$modelo/$id";
        $adjunto = Adjunto::findByName($path, $file)->first();
        if ($adjunto->owner != $user->dni) {
            return $this->sendFail("Sense permisos, no ets el propietari");
        }
        if ($adjunto) {
            if (AttachedFileService::delete($adjunto)) {
                return $this->sendResponse([], 'OK');
            }
            return $this->sendFail("Error a l'esborrar");
        }
        return $this->sendFail("No s'ha trobat el document");
    }

    public function attachFile(Request $request)
    {
        $user = $this->ApiUser($request);
        $path = "$request->modelo/$request->id";
        if (AttachedFileService::save($request->file('file'), $path, $user->dni)) {
            return $this->sendResponse(['data'=>'OK'], 'OK');
        } else {
            return $this->sendFail("No s'ha pogut completar l'operacio");
        }
    }

}
