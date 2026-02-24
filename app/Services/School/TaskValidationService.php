<?php

declare(strict_types=1);

namespace Intranet\Services\School;

use Intranet\Entities\Documento;
use Intranet\Entities\Programacion;
use Intranet\Entities\Profesor;
use Intranet\Entities\Reunion;

class TaskValidationService
{
    private const ACTA_DELEGADO = 5;
    private const ACTA_AVAL = 7;
    private const ACTA_FSE = 9;

    public function resolve(?string $action, ?string $dni = null): int
    {
        if (!$action) {
            return 0;
        }

        $dni = $dni ?? (authUser()->dni ?? null);
        if (!$dni) {
            return 0;
        }

        return match ($action) {
            'AvalPrg' => $this->avalPrg(),
            'EntrPrg' => $this->entrPrg(),
            'SegAval' => $this->segAval($dni),
            'ActAval' => $this->actAval($dni),
            'ActaDel' => $this->actaDel($dni),
            'ActaFSE' => $this->actaFse($dni),
            'InfDept' => $this->infDept($dni),
            default => 0,
        };
    }

    private function avalPrg(): int
    {
        foreach (Programacion::misProgramaciones()->get() as $programacion) {
            if (is_null($programacion->propuestas) || $programacion->propuestas === '') {
                return 0;
            }
        }

        return 1;
    }

    private function entrPrg(): int
    {
        foreach (Programacion::misProgramaciones()->get() as $programacion) {
            if ((int) $programacion->estado === 0) {
                return 0;
            }
        }

        return 1;
    }

    private function segAval(string $dni): int
    {
        foreach (app(ModuloGrupoService::class)->misModulos($dni) as $modulo) {
            if (!$modulo->resultados->where('evaluacion', '<=', evaluacion())) {
                return 0;
            }
        }

        return 1;
    }

    private function actAval(string $dni): int
    {
        $howManyAre = Reunion::convocante($dni)->tipo(self::ACTA_AVAL)->archivada()->count();

        return $howManyAre >= evaluacion() ? 1 : 0;
    }

    private function actaDel(string $dni): int
    {
        return Reunion::convocante($dni)->tipo(self::ACTA_DELEGADO)->archivada()->count();
    }

    private function actaFse(string $dni): int
    {
        return Reunion::convocante($dni)->tipo(self::ACTA_FSE)->archivada()->count();
    }

    private function infDept(string $dni): int
    {
        $fullName = Profesor::query()->find($dni)?->FullName ?? (authUser()->FullName ?? '');

        return Documento::where('propietario', $fullName)
            ->where('tipoDocumento', 'Acta')
            ->where('curso', Curso())
            ->where('descripcion', 'Informe Trimestral')
            ->count() >= evaluacion() ? 1 : 0;
    }
}
