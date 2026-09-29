<?php

declare(strict_types=1);

namespace Intranet\Application\Reunion;

use Illuminate\Support\Facades\DB;
use Intranet\Entities\Profesor;
use Intranet\Services\Document\TipoReunionService;

/**
 * Assigna números visibles consecutius a les actes per curs i òrgan.
 */
class ReunionActNumberService
{
    /**
     * Reserva el número següent dins de la transacció de creació.
     *
     * @return array{organo_acta: string, numero_acta: int}
     */
    public function next(
        string $course,
        int $type,
        ?string $groupCode,
        ?string $workGroup,
        Profesor $creator
    ): array {
        $organ = $this->organKey($type, $groupCode, $workGroup, $creator);

        DB::table('reunion_acta_counters')->insertOrIgnore([
            'curso' => $course,
            'organo' => $organ,
            'ultimo_numero' => 0,
        ]);

        $counter = DB::table('reunion_acta_counters')
            ->where('curso', $course)
            ->where('organo', $organ)
            ->lockForUpdate()
            ->first();

        $next = ((int) ($counter->ultimo_numero ?? 0)) + 1;

        DB::table('reunion_acta_counters')
            ->where('curso', $course)
            ->where('organo', $organ)
            ->update(['ultimo_numero' => $next]);

        return [
            'organo_acta' => $organ,
            'numero_acta' => $next,
        ];
    }

    /**
     * Construïx una identitat estable per a l'òrgan convocant.
     */
    private function organKey(
        int $type,
        ?string $groupCode,
        ?string $workGroup,
        Profesor $creator
    ): string {
        $collective = (string) (new TipoReunionService($type))->colectivo;

        return match ($collective) {
            'Grupo' => 'grup:' . $this->requiredKey($groupCode, (string) $creator->dni),
            'Departamento' => 'departament:' . $this->requiredKey(
                $creator->departamento !== null ? (string) $creator->departamento : null,
                (string) $creator->dni
            ),
            'Profesor' => 'claustre',
            'Jefe' => 'cocope',
            'GrupoTrabajo' => 'grup-treball:' . $this->requiredKey($workGroup, (string) $creator->dni),
            default => 'tipus:' . $type . ':' . (string) $creator->dni,
        };
    }

    /**
     * Usa una clau llegada estable quan falta la relació estructural.
     */
    private function requiredKey(?string $value, string $creatorId): string
    {
        $value = trim((string) $value);

        return $value !== '' ? $value : 'llegat-' . $creatorId;
    }
}
