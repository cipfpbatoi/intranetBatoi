<?php

declare(strict_types=1);

namespace Intranet\Application\AlumnoFct;

use Illuminate\Support\Facades\DB;
use Intranet\Entities\Alumno;
use Intranet\Entities\AlumnoFct;
use Intranet\Entities\Centro;
use Intranet\Entities\Ciclo;
use Intranet\Entities\Colaboracion;
use Intranet\Entities\Empresa;
use Intranet\Entities\Fct;
use Intranet\Entities\Instructor;

/**
 * Importa estades FE ja llegides d'ITACA sense modificar el flux SAO llegat.
 *
 * ITACA no aporta les dades obligatòries per donar d'alta empreses o
 * instructors nous; per això només relaciona fitxes locals ja completes.
 */
final class ItacaFeImportService
{
    /**
     * Importa les estades que es poden relacionar de forma segura.
     */
    public function import(ItacaFctImportRecord $record, string $idProfesor): ItacaFeImportResult
    {
        $result = new ItacaFeImportResult();
        $alumne = Alumno::find($record->nia);
        $cicle = Ciclo::query()->where('ciclo', $record->cicle)->first();

        if ($alumne === null) {
            $result->addPending("No s'ha trobat l'alumnat amb NIA {$record->nia}.");

            return $result;
        }
        if ($cicle === null) {
            $result->addPending("No s'ha trobat el cicle d'ITACA {$record->cicle}.");

            return $result;
        }

        foreach ($record->estades as $estada) {
            $this->importPlacement($record, $estada, $alumne, $cicle, $idProfesor, $result);
        }

        return $result;
    }

    private function importPlacement(
        ItacaFctImportRecord $record,
        ItacaFePlacement $estada,
        Alumno $alumne,
        Ciclo $cicle,
        string $idProfesor,
        ItacaFeImportResult $result
    ): void {
        $key = $record->identityKey($estada);
        if (AlumnoFct::query()->where('itacaImportKey', $key)->exists()) {
            $result->existents++;

            return;
        }

        $empresa = Empresa::query()->where('cif', $estada->cifEmpresa)->first();
        if ($empresa === null) {
            $result->addPending("No existeix l'empresa amb CIF {$estada->cifEmpresa}; cal completar-la abans d'importar l'estada.");

            return;
        }
        $centre = Centro::query()
            ->where('idEmpresa', $empresa->id)
            ->where('nombre', $estada->centreTreball)
            ->first();
        if ($centre === null) {
            $result->addPending("No existeix el centre de treball {$estada->centreTreball} per a l'empresa {$estada->cifEmpresa}.");

            return;
        }
        $instructor = Instructor::find($estada->nifInstructor);
        if ($instructor === null) {
            $result->addPending("No existeix l'instructor amb NIF {$estada->nifInstructor}; cal completar-lo abans d'importar l'estada.");

            return;
        }
        $colaboracio = Colaboracion::query()
            ->where('idCentro', $centre->id)
            ->where('idCiclo', $cicle->id)
            ->first();
        if ($colaboracio === null) {
            $result->addPending("No hi ha col·laboració del centre {$estada->centreTreball} amb el cicle {$record->cicle}.");

            return;
        }

        DB::transaction(function () use ($record, $estada, $alumne, $idProfesor, $key, $colaboracio, $instructor, $result): void {
            if (AlumnoFct::query()->where('itacaImportKey', $key)->exists()) {
                $result->existents++;

                return;
            }
            $fct = Fct::create([
                'idColaboracion' => $colaboracio->id,
                'idInstructor' => $instructor->dni,
                'asociacion' => 4,
            ]);
            AlumnoFct::query()->create([
                'idFct' => $fct->id,
                'idAlumno' => $alumne->nia,
                'idProfesor' => $idProfesor,
                'desde' => $record->dataInici,
                'hasta' => $record->dataFi,
                'horas' => $record->horesPrevistes,
                'autorizacion' => $record->autoritzacio,
                'itacaImportKey' => $key,
            ]);
            $result->creades++;
        });
    }
}
