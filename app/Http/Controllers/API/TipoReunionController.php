<?php

namespace Intranet\Http\Controllers\API;

use Intranet\Entities\TipoReunion;
use Illuminate\Http\Request;

class TipoReunionController extends ApiBaseController
{

    public function show($id,$send=true)
    {
        $data = TipoReunion::find($id);
        return $this->sendResponse($data->get(), 'OK');
    }

}
