<?php

declare(strict_types=1);

namespace Intranet\Application\Fct;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Intranet\Services\Document\DocumentService;
use Intranet\Support\Fct\DocumentoFctConfig;

/**
 * Resol les opcions seleccionables de documentació FCT a partir de la seua configuració.
 */
class FctDocumentOptionsService
{
    /**
     * Carrega les opcions de selecció per a un codi de document FCT.
     *
     * @param string $documento
     * @return AnonymousResourceCollection
     */
    public function optionsFor(string $documento): AnonymousResourceCollection
    {
        $config = new DocumentoFctConfig($documento);
        $finder = $config->getFinder();
        $resource = $config->getResource();
        $service = new DocumentService(new $finder($config));

        return $resource::collection($service->load());
    }
}
