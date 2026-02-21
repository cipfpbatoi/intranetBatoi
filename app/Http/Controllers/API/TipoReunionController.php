<?php

namespace Intranet\Http\Controllers\API;

use Intranet\Services\Document\TipoReunionService;

class TipoReunionController extends ApiResourceController
{

    public function show($id)
    {
        $data = TipoReunionService::find($id);
        return $this->sendResponse($data->get(), 'OK');
    }

}
