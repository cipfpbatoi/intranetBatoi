<?php

namespace Intranet\Http\Controllers\Direccion\Comision;

use Intranet\Entities\Comision;
use Intranet\Http\Controllers\Controller;
use Intranet\Services\General\AutorizacionPrintService;
use Intranet\Services\UI\AppAlert as Alert;

/**
 * Impressió específica de pagaments del panell Livewire de comissions de Direcció.
 *
 * Manté el flux funcional del pilot nou sense dependre del controller legacy
 * generalista de comissions.
 */
class PaymentPrintController extends Controller
{
    private AutorizacionPrintService $autorizacionPrintService;

    public function __construct(AutorizacionPrintService $autorizacionPrintService)
    {
        parent::__construct();
        $this->autorizacionPrintService = $autorizacionPrintService;
    }

    /**
     * Genera el PDF de pagaments preparats (`estado = 6`) i els deixa com a cobrats.
     *
     * @return mixed
     */
    public function __invoke()
    {
        $this->authorize('create', Comision::class);

        $response = $this->autorizacionPrintService->imprimir(
            Comision::class,
            'Comision',
            'payments',
            6,
            5,
            'landscape',
            false
        );

        if ($response) {
            return $response;
        }

        Alert::info(trans('messages.generic.empty'));

        return back();
    }
}
