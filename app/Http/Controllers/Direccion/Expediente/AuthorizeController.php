<?php

namespace Intranet\Http\Controllers\Direccion\Expediente;

use Intranet\Http\Controllers\Controller;
use Intranet\Services\School\ExpedienteWorkflowService;

/**
 * Autoritza en bloc els expedients pendents des de Direcció.
 */
class AuthorizeController extends Controller
{
    /**
     * Executa l'autorització massiva d'expedients pendents.
     */
    public function __invoke()
    {
        app(ExpedienteWorkflowService::class)->authorizePending();

        return back();
    }
}
