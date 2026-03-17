<?php

namespace Intranet\Http\Controllers\Direccion\Expediente;

use Illuminate\Support\Str;
use Intranet\Application\Expediente\ExpedienteService;
use Intranet\Http\Controllers\Controller;
use Intranet\Http\Traits\Core\Imprimir;
use Intranet\Services\General\GestorService;
use Intranet\Services\General\StateService;
use Intranet\Services\UI\AppAlert as Alert;

/**
 * Genera el PDF col·lectiu d'expedients autoritzats des de Direcció.
 */
class PrintController extends Controller
{
    use Imprimir;

    /**
     * Genera el PDF i marca com impresos els expedients inclosos.
     */
    public function __invoke(ExpedienteService $expedienteService)
    {
        $expedientes = $expedienteService->readyToPrint();

        if ($expedientes->count()) {
            foreach ($expedienteService->allTypes() as $tipo) {
                $todos = $expedientes->where('tipo', $tipo->id);

                if ($todos->count()) {
                    $pdf = self::hazPdf("pdf.expediente.$tipo->vista", $todos);
                    $nom = 'Expediente_' . Str::slug($tipo->titulo, '_') . '_' . now()->format('Ymd_His') . '.pdf';
                    $nomComplet = 'gestor/' . Curso() . '/informes/' . $nom;
                    $tags = "listado llistat expediente expedient $tipo->titulo";

                    $gestor = new GestorService();
                    $doc = $gestor->save(['fichero' => $nomComplet, 'tags' => $tags]);

                    StateService::makeAll($todos, '_print');
                    StateService::makeLink($todos, $doc);

                    $pdf->save(storage_path('/app/' . $nomComplet));

                    return response()->download(storage_path('/app/' . $nomComplet), $nom);
                }
            }
        }

        Alert::info(trans('messages.generic.empty'));

        return back();
    }
}
