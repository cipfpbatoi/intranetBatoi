<?php

namespace Intranet\Http\Controllers\API;

use Intranet\Componentes\DocumentoFct;
use Intranet\Finders\MailFinders\MySignaturesFinder;
use Intranet\Finders\MailFinders\SignaturesFinder;
use Intranet\Services\DocumentService;


class DocumentacionFCTController
{
    public function exec($documento)
    {
        $documento = new DocumentoFct($documento);
        $finder = $documento->getFinder();
        $resource = $documento->getResource();
        $service = new DocumentService(new $finder($documento));
        return $resource::collection($service->load());
    }

    public function signatura()
    {
        $finder = new MySignaturesFinder();
        return $finder->getElements();
    }

    public function signaturaDirector()
    {
        $finder = new SignaturesFinder();
        return $finder->getElements();
    }

}
