<?php
namespace Intranet\Services\Mail;

use Illuminate\Http\JsonResponse;
use Intranet\Finders\UniqueFinder;
use Intranet\Finders\RequestFinder;
use Intranet\Support\Fct\DocumentoFctConfig;
use Intranet\Services\Document\DocumentService;

class FctMailService
{
    /**
     * Obté un correu per ID.
     *
     * @param int $id
     * @param string $documento
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function getMailById(int $id, string $documento)
    {
        $finder = new UniqueFinder(['id' => $id, 'document' => new DocumentoFctConfig($documento)]);
        return $this->generateMail($finder);
    }

    /**
     * Obté un correu a partir d'una petició.
     *
     * @param \Illuminate\Http\Request $request
     * @param string $documento
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function getMailByRequest($request, string $documento)
    {
        $finder = new RequestFinder(['request' => $request, 'document' => new DocumentoFctConfig($documento)]);
        return $this->generateMail($finder);
    }

    /**
     * Genera el correu a partir d'un Finder.
     *
     * @param mixed $finder
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    private function generateMail($finder)
    {
        $service = new DocumentService($finder);
        $response = $service->render();

        // Gestiona possibles errors retornats per `render()`
        if ($response instanceof  JsonResponse && $response->getStatusCode() !== 200) {
            return back()->with('error', $response->getData()->error);
        }

        return $response;
    }
}
