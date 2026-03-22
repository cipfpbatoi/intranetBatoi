<?php

namespace Intranet\Http\Controllers\API;

use Intranet\Application\Fct\FctDocumentOptionsService;
use Intranet\Support\Fct\DocumentoFctConfig;
use Intranet\Finders\MailFinders\MyA1Finder;
use Intranet\Finders\MailFinders\MySignaturesFinder;
use Intranet\Finders\MailFinders\SignaturesFinder;

/**
 * Exposa opcions i dades auxiliars per a la documentació FCT.
 */
class DocumentacionFCTController
{
    private ?FctDocumentOptionsService $fctDocumentOptionsService = null;

    public function __construct(?FctDocumentOptionsService $fctDocumentOptionsService = null)
    {
        $this->fctDocumentOptionsService = $fctDocumentOptionsService;
    }

    private function options(): FctDocumentOptionsService
    {
        if ($this->fctDocumentOptionsService === null) {
            $this->fctDocumentOptionsService = app(FctDocumentOptionsService::class);
        }

        return $this->fctDocumentOptionsService;
    }

    public function exec($documento)
    {
        return $this->options()->optionsFor((string) $documento);
    }

    /**
     * Retorna signatures disponibles per a documents estàndard d'FCT.
     */
    public function signatura()
    {
        $finder = new MySignaturesFinder();
        return $finder->getElements();
    }

    /**
     * Retorna signatures específiques per a l'annex A1.
     */
    public function signaturaA1()
    {
        $finder = new MyA1Finder();
        return $finder->getElements();
    }

    /**
     * Retorna signatures de direcció per als documents que les requerixen.
     */
    public function signaturaDirector()
    {
        $finder = new SignaturesFinder();
        return $finder->getElements();
    }

}
