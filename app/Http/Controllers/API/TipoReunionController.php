<?php

namespace Intranet\Http\Controllers\API;

use Intranet\Services\Document\TipoReunionService;
use Illuminate\Http\Request;

class TipoReunionController extends ApiBaseController
{

    public function show($id,$send=true)
    {
        $data = TipoReunionService::find($id);
        return $this->sendResponse($data->get(), 'OK');
    }

}
