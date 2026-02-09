<?php

namespace Intranet\Http\Controllers\API;

use Intranet\Support\Fct\DocumentoFctConfig;
use Intranet\Finders\MailFinders\MyA1Finder;
use Intranet\Finders\MailFinders\MySignaturesFinder;
use Intranet\Finders\MailFinders\SignaturesFinder;
use Intranet\Services\DocumentService;


class DocumentacionFCTController
{
    public function exec($documento)
    {
        $documento = new DocumentoFctConfig($documento);
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

    public function signaturaA1()
    {
        $finder = new MyA1Finder();
        return $finder->getElements();
    }

    public function signaturaDirector()
    {
        $finder = new SignaturesFinder();
        return $finder->getElements();
    }

}
