<?php

declare(strict_types=1);

namespace Intranet\Application\Convalidacio;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Intranet\Entities\CorrespondenciaCicleAngles;
use RuntimeException;

/** Gestiona i importa les correspondències de cicles d'anglés qualificats. */
class ConvalidacioAnglesCorrespondenciesService
{
    private const CAPCALERA = [
        'codi_cicle_angles',
        'nom_cicle_angles_val',
        'nom_cicle_angles_cas',
        'codi_cicle_contenidor',
        'nom_cicle_contenidor_val',
        'nom_cicle_contenidor_cas',
        'es_grau_superior',
    ];

    /** Importa el CSV i reemplaça, de manera atòmica, els cicles contenidors presents. */
    public function importar(UploadedFile $fitxer): int
    {
        $files = $this->llegirCsv($fitxer);
        if ($files === []) {
            throw new ConvalidacioException('El CSV no conté cap correspondència.');
        }

        DB::transaction(function () use ($files): void {
            $grups = collect($files)->groupBy('codi_cicle_contenidor');
            foreach ($grups as $codiContenidor => $correspondencies) {
                CorrespondenciaCicleAngles::query()
                    ->where('codi_cicle_contenidor', $codiContenidor)
                    ->delete();

                foreach ($correspondencies as $correspondencia) {
                    CorrespondenciaCicleAngles::query()->create($correspondencia);
                }
            }
        });

        return count($files);
    }

    /** Retorna el cicle d'anglés corresponent a un codi XML exacte. */
    public function trobarPerCicleAngles(string $codi): ?CorrespondenciaCicleAngles
    {
        $correspondencies = CorrespondenciaCicleAngles::query()
            ->where('codi_cicle_angles', $codi)
            ->limit(2)
            ->get();

        return $correspondencies->count() === 1 ? $correspondencies->first() : null;
    }

    /** Llig i valida tot el CSV abans de permetre qualsevol substitució. */
    private function llegirCsv(UploadedFile $fitxer): array
    {
        $handle = fopen($fitxer->getRealPath(), 'rb');
        if ($handle === false) {
            throw new ConvalidacioException('No s’ha pogut llegir el fitxer CSV.');
        }

        try {
            $primeraLinia = fgets($handle);
            if ($primeraLinia === false) {
                throw new ConvalidacioException('El fitxer CSV està buit.');
            }
            $delimitador = substr_count($primeraLinia, ';') > substr_count($primeraLinia, ',') ? ';' : ',';
            rewind($handle);
            $capcalera = fgetcsv($handle, null, $delimitador, '"', '\\');
            if (!is_array($capcalera)) {
                throw new ConvalidacioException('El CSV no conté una capçalera vàlida.');
            }
            $capcalera[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string) $capcalera[0]);
            $capcalera = array_map(static fn ($camp): string => trim((string) $camp), $capcalera);
            if ($capcalera !== self::CAPCALERA) {
                throw new ConvalidacioException('La capçalera del CSV no coincidix amb el format esperat.');
            }

            $files = [];
            $parelles = [];
            $numeroLinia = 1;
            while (($valors = fgetcsv($handle, null, $delimitador, '"', '\\')) !== false) {
                $numeroLinia++;
                if ($valors === [null] || $valors === []) {
                    continue;
                }
                if (count($valors) !== count(self::CAPCALERA)) {
                    throw new ConvalidacioException("La fila {$numeroLinia} no té el nombre de columnes esperat.");
                }

                $fila = array_combine(self::CAPCALERA, array_map(static fn ($valor): string => trim((string) $valor), $valors));
                $files[] = $this->validarFila($fila, $numeroLinia);
                $clau = $fila['codi_cicle_angles'] . '|' . $fila['codi_cicle_contenidor'];
                if (isset($parelles[$clau])) {
                    throw new ConvalidacioException("La fila {$numeroLinia} duplica una correspondència del CSV.");
                }
                $parelles[$clau] = true;
            }

            return $files;
        } catch (RuntimeException $exception) {
            if ($exception instanceof ConvalidacioException) {
                throw $exception;
            }
            throw new ConvalidacioException('No s’ha pogut interpretar el fitxer CSV.');
        } finally {
            fclose($handle);
        }
    }

    /** Valida i normalitza una fila del CSV. */
    private function validarFila(array $fila, int $numeroLinia): array
    {
        foreach (self::CAPCALERA as $camp) {
            if ($fila[$camp] === '') {
                throw new ConvalidacioException("El camp {$camp} està buit en la fila {$numeroLinia}.");
            }
        }

        foreach (['codi_cicle_angles', 'codi_cicle_contenidor'] as $camp) {
            if (strlen($fila[$camp]) > 50) {
                throw new ConvalidacioException("El codi {$camp} supera els 50 caràcters en la fila {$numeroLinia}.");
            }
        }
        foreach (['nom_cicle_angles_val', 'nom_cicle_angles_cas', 'nom_cicle_contenidor_val', 'nom_cicle_contenidor_cas'] as $camp) {
            if (mb_strlen($fila[$camp]) > 255) {
                throw new ConvalidacioException("El camp {$camp} supera els 255 caràcters en la fila {$numeroLinia}.");
            }
        }

        $fila['es_grau_superior'] = match (mb_strtolower($fila['es_grau_superior'])) {
            '1', 'si', 'sí' => true,
            '0', 'no' => false,
            default => throw new ConvalidacioException("El camp es_grau_superior ha de ser 1/0 o si/no en la fila {$numeroLinia}."),
        };

        return $fila;
    }
}
