<?php

namespace Intranet\Services\School;

use Intranet\Entities\Falta;
use Illuminate\Support\Carbon;
use Intranet\Services\General\StateService;

class FaltaReportService
{
    public function getComunicacioElements(Carbon $desde, Carbon $hasta)
    {
        return $this->buildQuery($desde, $hasta, '5')
            ->orderBy('idProfesor')
            ->orderBy('desde')
            ->get();
    }

    public function getMensualElements(Carbon $desde, Carbon $hasta)
    {
        return $this->buildQuery($desde, $hasta, '4')
            ->orderBy('idProfesor')
            ->orderBy('desde')
            ->get();
    }

    public function markPrinted(Carbon $hasta): void
    {
        foreach (Falta::where([
            ['estado', '>', '0'],
            ['estado', '<', '4'],
            ['hasta', '<=', $hasta]
        ])->get() as $elemento) {
            $staSer = new StateService($elemento);
            $staSer->_print();
        }
    }

    public function nameFile(): string
    {
        return 'gestor/' . Curso() . '/informes/' . 'Falta' . new Carbon() . '.pdf';
    }

    private function buildQuery(Carbon $desde, Carbon $hasta, string $estadoUpper)
    {
        return Falta::where(function ($query) use ($desde, $hasta, $estadoUpper) {
            $query->where(function ($query) use ($desde, $hasta, $estadoUpper) {
                $query->where('estado', '>', '0')
                    ->where('estado', '<', $estadoUpper)
                    ->where(function ($query) use ($desde, $hasta) {
                        $query->whereBetween('desde', [$desde, $hasta])
                            ->orWhereBetween('hasta', [$desde, $hasta]);
                    });
            })->orWhere(function ($query) use ($hasta) {
                $query->where('estado', '=', '5')
                    ->where('desde', '<=', $hasta);
            });
        });
    }
}
