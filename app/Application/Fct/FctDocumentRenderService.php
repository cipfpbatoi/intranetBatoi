<?php

declare(strict_types=1);

namespace Intranet\Application\Fct;

use Illuminate\Http\JsonResponse;
use Intranet\Finders\RequestFinder;
use Intranet\Finders\UniqueFinder;
use Intranet\Services\Document\DocumentService;
use Intranet\Support\Fct\DocumentoFctConfig;

/**
 * Orquestra el renderitzat o enviament de documentació FCT des de web.
 */
class FctDocumentRenderService
{
    /**
     * Genera la resposta documental per a un únic element.
     *
     * @param int $id
     * @param string $documento
     * @return mixed
     */
    public function renderById(int $id, string $documento)
    {
        $finder = new UniqueFinder(['id' => $id, 'document' => new DocumentoFctConfig($documento)]);

        return $this->renderFromFinder($finder);
    }

    /**
     * Genera la resposta documental a partir d'una selecció de petició.
     *
     * @param mixed $request
     * @param string $documento
     * @return mixed
     */
    public function renderByRequest($request, string $documento)
    {
        $finder = new RequestFinder(['request' => $request, 'document' => new DocumentoFctConfig($documento)]);

        return $this->renderFromFinder($finder);
    }

    /**
     * @param mixed $finder
     * @return mixed
     */
    private function renderFromFinder($finder)
    {
        $service = $this->makeDocumentService($finder);
        $response = $service->render();

        if ($response instanceof JsonResponse && $response->getStatusCode() !== 200) {
            return back()->with('error', $response->getData()->error);
        }

        return $response;
    }

    /**
     * Crea el servei documental per al finder resolt.
     *
     * @param mixed $finder
     */
    protected function makeDocumentService($finder): DocumentService
    {
        return new DocumentService($finder);
    }
}
