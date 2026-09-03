<?php

namespace Intranet\Http\Controllers\Direccion\Reunion;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Intranet\Application\Reunion\ReunionActaExportService;
use Intranet\Entities\Documento;
use Intranet\Http\Controllers\Controller;
use Intranet\Services\UI\AppAlert as Alert;
use RuntimeException;

/**
 * Descarrega en ZIP les actes d'avaluació arxivades al gestor documental.
 */
class ActesAvaluacioZipController extends Controller
{
    private ?ReunionActaExportService $exports = null;

    public function __construct(?ReunionActaExportService $exports = null)
    {
        parent::__construct();
        $this->exports = $exports;
    }

    /**
     * Genera la descàrrega de les actes de l'avaluació seleccionada.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\RedirectResponse
     */
    public function __invoke(Request $request)
    {
        Gate::authorize('viewAny', Documento::class);

        $numero = (int) $request->input('numero');
        if (!$this->exports()->isValidEvaluation($numero)) {
            Alert::warning('Selecciona una avaluació vàlida.');
            return back();
        }

        try {
            $result = $this->exports()->export($numero);
        } catch (RuntimeException $exception) {
            report($exception);
            Alert::danger($exception->getMessage());
            return back();
        }

        if ($result['path'] === null) {
            Alert::warning('No hi ha actes arxivades disponibles per a eixa avaluació.');
            return back();
        }

        if ($result['missing'] !== []) {
            Alert::warning("S'han omés alguns fitxers que no existien en el gestor documental.");
        }

        return response()
            ->download($result['path'], $result['filename'], ['Content-Type' => 'application/zip'])
            ->deleteFileAfterSend(true);
    }

    /**
     * Retorna el servei d'exportació d'actes.
     */
    private function exports(): ReunionActaExportService
    {
        if ($this->exports === null) {
            $this->exports = app(ReunionActaExportService::class);
        }

        return $this->exports;
    }
}
