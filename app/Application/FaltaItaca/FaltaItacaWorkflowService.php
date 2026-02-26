<?php

declare(strict_types=1);

namespace Intranet\Application\FaltaItaca;

use Intranet\Entities\Documento;
use Intranet\Entities\Falta_itaca;
use Intranet\Services\General\StateService;
use Illuminate\Support\Carbon;

class FaltaItacaWorkflowService
{
    public function findElements(string $desde, string $hasta)
    {
        return Falta_itaca::where([
            ['estado', '2'],
            ['dia', '>=', FechaInglesa($desde)],
            ['dia', '<=', FechaInglesa($hasta)],
        ])->join('profesores', 'profesores.dni', '=', 'faltas_itaca.idProfesor')
            ->orderBy('profesores.apellido1')
            ->orderBy('profesores.apellido2')
            ->orderBy('profesores.nombre')
            ->orderBy('dia')
            ->get();
    }

    public function monthlyReportFileName(string $desde): string
    {
        $fecha = new Carbon($desde);
        return 'gestor/' . Curso() . '/informes/' . 'Birret' . $fecha->format('M') . '.pdf';
    }

    public function deletePreviousMonthlyReport(string $path): void
    {
        $pathService = new \Intranet\Services\Document\DocumentPathService();
        if ($doc = Documento::where('fichero', $path)->first()) {
            if ($pathService->existsPath(storage_path('app/' . $doc->fichero))) {
                unlink(storage_path('app/' . $doc->fichero));
            }
            $doc->delete();
        }
    }

    public function resolveByAbsenceId(int|string $id): bool
    {
        $falta = Falta_itaca::find($id);
        if (!$falta) {
            return false;
        }

        $faltesDia = Falta_itaca::where('idProfesor', $falta->idProfesor)
            ->where('dia', FechaInglesa($falta->dia))
            ->where('estado', 1)
            ->get();

        foreach ($faltesDia as $faltaHora) {
            (new StateService($faltaHora))->resolve();
        }

        return true;
    }

    public function refuseByAbsenceId(int|string $id, ?string $explicacion = null): bool
    {
        $falta = Falta_itaca::find($id);
        if (!$falta) {
            return false;
        }

        $faltesDia = Falta_itaca::where('idProfesor', $falta->idProfesor)
            ->where('dia', FechaInglesa($falta->dia))
            ->get();

        foreach ($faltesDia as $faltaHora) {
            (new StateService($faltaHora))->refuse($explicacion);
        }

        return true;
    }
}
