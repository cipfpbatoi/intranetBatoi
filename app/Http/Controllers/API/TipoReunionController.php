<?php

namespace Intranet\Http\Controllers\API;

use Intranet\Entities\TipoReunion;
use Illuminate\Http\Request;

class TipoReunionController extends ApiBaseController
{

    public function show($id,$send=true)
    {
        $data = TipoReunion::all()[$id];
        return $this->sendResponse($data, 'OK');
    }

}
