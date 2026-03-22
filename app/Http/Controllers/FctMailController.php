<?php

namespace Intranet\Http\Controllers;

use Intranet\Application\Fct\FctDocumentRenderService;
use Illuminate\Http\Request;

/**
 * Controlador web per a renderitzar documentació FCT sota demanda.
 */
class FctMailController extends Controller
{
    private ?FctDocumentRenderService $fctDocumentRenderService = null;

    public function __construct(?FctDocumentRenderService $fctDocumentRenderService = null)
    {
        parent::__construct();
        $this->fctDocumentRenderService = $fctDocumentRenderService;
    }

    private function documents(): FctDocumentRenderService
    {
        if ($this->fctDocumentRenderService === null) {
            $this->fctDocumentRenderService = app(FctDocumentRenderService::class);
        }

        return $this->fctDocumentRenderService;
    }

    public function showMailById($id, $documento)
    {
        return $this->documents()->renderById((int) $id, (string) $documento);
    }

    /**
     * Renderitza un document FCT a partir dels paràmetres rebuts en la petició.
     */
    public function showMailByRequest(Request $request, $documento)
    {
        return $this->documents()->renderByRequest($request, (string) $documento);
    }
}
