<?php

namespace Intranet\Http\Controllers\API;

use Intranet\Botones\DocumentoFct;
use Intranet\Services\DocumentService;


class DocumentacionFCTController
{
    public function exec($documento){
        $documento = new DocumentoFct($documento);
        $finder = $documento->getFinder();
        $resource = $documento->getResource();

        $service = new DocumentService(new $finder($documento));
        return $resource::collection($service->load());
    }

}
