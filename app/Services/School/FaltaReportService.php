<?php

namespace Intranet\Services\School;

use Intranet\Entities\Falta;
use Jenssegers\Date\Date;
use Intranet\Services\General\StateService;

class FaltaReportService
{
    public function getComunicacioElements(Date $desde, Date $hasta)
    {
        return $this->buildQuery($desde, $hasta, '5')
            ->orderBy('idProfesor')
            ->orderBy('desde')
            ->get();
    }

    public function getMensualElements(Date $desde, Date $hasta)
    {
        return $this->buildQuery($desde, $hasta, '4')
            ->orderBy('idProfesor')
            ->orderBy('desde')
            ->get();
    }

    public function markPrinted(Date $hasta): void
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
        return 'gestor/' . Curso() . '/informes/' . 'Falta' . new Date() . '.pdf';
    }

    private function buildQuery(Date $desde, Date $hasta, string $estadoUpper)
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
