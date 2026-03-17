<?php

namespace Intranet\Http\Controllers\Direccion\Comision;

use Intranet\Entities\Comision;
use Intranet\Http\Controllers\Controller;
use Intranet\Services\General\AutorizacionPrintService;
use Intranet\Services\UI\AppAlert as Alert;

/**
 * Impressió específica del panell Livewire de comissions de Direcció.
 *
 * Actua com a bridge fi entre el pilot nou i el servei d'impressió en lot,
 * evitant dependre del controller legacy generalista.
 */
class PrintController extends Controller
{
    private AutorizacionPrintService $autorizacionPrintService;

    public function __construct(AutorizacionPrintService $autorizacionPrintService)
    {
        parent::__construct();
        $this->autorizacionPrintService = $autorizacionPrintService;
    }

    /**
     * Genera el PDF de comissions autoritzades des del panell Livewire.
     *
     * @return mixed
     */
    public function __invoke()
    {
        $this->authorize('create', Comision::class);

        $response = $this->autorizacionPrintService->imprimir(
            Comision::class,
            'Comision',
            'comisionsServei'
        );

        if ($response) {
            return $response;
        }

        Alert::info(trans('messages.generic.empty'));

        return back();
    }
}
