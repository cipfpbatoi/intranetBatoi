<?php

declare(strict_types=1);

namespace Intranet\Application\Convalidacio;

use DomainException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intranet\Entities\Alumno;
use Intranet\Entities\CicleFormatiuCursat;
use Intranet\Entities\Convalidacio;
use Inranet\Entities\Modulo;
use Intranet\Entities\SollicitudConvalidacio;

/**
 * Casos d'ús del cicle de vida de les convalidacions.
 */
class ConvalidacioService
{
    public const CERTIFICATS_PATH = 'convalidacions/certificats';

    public function __construct(
        private readonly ConvalidacioQueryService $queryService
    ) {
    }

    /**
     * Crea una sol·licitud de convalidació amb varis mòduls.
     *
     * @param array<array{modulo_id: string|null, tipus_convalidacio: string, cicle_formatiu_cursat_id: int|null, certificat_path: string|null, certificat_autentic: bool|null}> $items
     *
     * @throws ConvalidacioException
     */
    public function tramitar(string $alumnoId, array $items): SollicitudConvalidacio
    {
        return DB::transaction(function () use ($alumnoId, $items): SollicitudConvalidacio {
            $alumno = Alumno::query()->findOrFail($alumnoId);

            $sollicitud = SollicitudConvalidacio::query()->create([
                'alumno_id' => $alumnoId,
                'estat' => SollicitudConvalidacio::ESTAT_PENDENT,
                'data_sol·licitud' => now(),
            ]);

            foreach ($items as $index => $item) {
                $this->validarItem($item, $alumno);
                $convalidacio = $this->crearConvalidacio($sollicitud, $item, $index);
                $sollicitud->convalidacions()->save($convalidacio);
            }

            return $sollicitud->fresh();
        }, 3);
    }

    /**
     * Valida un item abans de crear la convalidació.
     *
     * @param array{modulo_id: string|null, tipus_convalidacio: string, cicle_formatiu_cursat_id: int|null, certificat_path: string|null, certificat_autentic: bool|null} $item
     *
     * @throws ConvalidacioException
     */
    private function validarItem(array $item, Alumno $alumno): void
    {
        $tipus = $item['tipus_convalidacio'] ?? null;

        if (!in_array($tipus, Convalidacio::getTipusConvalidacioOptions())) {
            throw new ConvalidacioException('Tipus de convalidació invàlid.');
        }

        match ($tipus) {
            Convalidacio::TIPUS_MATEIX_CENTRE => $this->validarMateixCentre($item, $alumno),
            Convalidacio::TIPUS_ALTRE_CENTRE, Convalidacio::TIPUS_ESCOOLA_IDIOMES => $this->validarCertificat($item),
            Convalidacio::TIPUS_TITOL_UNIVERSITARI, Convalidacio::TIPUS_TITOL_FP => throw new ConvalidacioException(
                'Aquesta convalidació no es pot fer a través d\'aquesta interfície. ' .
                'Per favor, contacta amb la secretaria per a més informació.'
            ),
            default => throw new ConvalidacioException('Tipus de convalidació invàlid.')
        };
    }

    /**
     * Valida una convalidació per mateix centre.
     *
     * @param array{modulo_id: string|null, tipus_convalidacio: string, cicle_formatiu_cursat_id: int|null, certificat_path: string|null, certificat_autentic: bool|null} $item
     *
     * @throws ConvalidacioException
     */
    private function validarMateixCentre(array $item, Alumno $alumno): void
    {
        if (blank($item['modulo_id'])) {
            throw new ConvalidacioException('El mòdul és obligatori per convalidació del mateix centre.');
        }

        if (blank($item['cicle_formatiu_cursat_id'])) {
            throw new ConvalidacioException('El cicle formatiu cursat és obligatori per convalidació del mateix centre.');
        }

        $cicleCursat = CicleFormatiuCursat::query()->findOrFail($item['cicle_formatiu_cursat_id']);

        if ((string) $cicleCursat->alumno_id !== (string) $alumno->nia) {
            throw new ConvalidacioException('El cicle formatiu cursat no pertany a este alumne.');
        }

        $modulo = Modulo::query()->findOrFail($item['modulo_id']);

        $grupsAlumne = $alumno->Grupo()->pluck('id')->toArray();
        $grupsModulo = $modulo->Grupos()->pluck('id')->toArray();

        if (count(array_intersect($grupsAlumne, $grupsModulo)) === 0) {
            throw new ConvalidacioException('El mòdul no forma part del cicle actual de l\'alumne.');
        }
    }

    /**
     * Valida una convalidació amb certificat.
     *
     * @param array{modulo_id: string|null, tipus_convalidacio: string, cicle_formatiu_cursat_id: int|null, certificat_path: string|null, certificat_autentic: bool|null} $item
     *
     * @throws ConvalidacioException
     */
    private function validarCertificat(array $item): void
    {
        if (blank($item['certificat_path'])) {
            throw new ConvalidacioException('El certificat és obligatori per este tipus de convalidació.');
        }

        if ($item['certificat_autentic'] !== true) {
            throw new ConvalidacioException('Has d\'indicar que la informació és autèntica.');
        }
    }

    /**
     * Crea una convalidació amb les dades proporcionades.
     *
     * @param array{modulo_id: string|null, tipus_convalidacio: string, cicle_formatiu_cursat_id: int|null, certificat_path: string|null, certificat_autentic: bool|null} $item
     */
    private function crearConvalidacio(
        SollicitudConvalidacio $sollicitud,
        array $item,
        int $index
    ): Convalidacio {
        $convalidacio = new Convalidacio([
            'tipus_convalidacio' => $item['tipus_convalidacio'],
            'certificat_path' => $item['certificat_path'] ?? null,
            'certificat_autentic' => $item['certificat_autentic'] ?? null,
            'estat' => Convalidacio::ESTAT_PENDENT,
        ]);

        if ($item['modulo_id'] ?? null) {
            $convalidacio->modulo_id = $item['modulo_id'];
        }

        if ($item['cicle_formatiu_cursat_id'] ?? null) {
            $convalidacio->cicle_formatiu_cursat_id = $item['cicle_formatiu_cursat_id'];
        }

        return $convalidacio;
    }

    /**
     * Canvia l'estat d'una sol·licitud (aprovar/rebutjar).
     *
     * @throws ConvalidacioException
     */
    public function canviarEstat(int $sollicitudId, string $estat, string $observacions = ''): SollicitudConvalidacio
    {
        if (!in_array($estat, [SollicitudConvalidacio::ESTAT_APROVAT, SollicitudConvalidacio::ESTAT_REBUTJAT, SollicitudConvalidacio::ESTAT_DOCUMENTS_REQUERITS])) {
            throw new ConvalidacioException('Estat invàlid.');
        }

        return DB::transaction(function () use ($sollicitudId, $estat, $observacions): SollicitudConvalidacio {
            $sollicitud = SollicitudConvalidacio::query()->lockForUpdate()->findOrFail($sollicitudId);

            if (!$sollicitud->estaPendent()) {
                throw new ConvalidacioException('Només es pot canviar l\'estat d\'una sol·licitud pendent.');
            }

            $sollicitud->forceFill([
                'estat' => $estat,
                'observacions' => trim($observacions),
                'data_resolucio' => now(),
            ])->save();

            return $sollicitud->fresh();
        });
    }

    /**
     * Descarrega un document adjunt.
     *
     * @throws ConvalidacioException
     */
    public function descarregarDocument(int $convalidacioId): string
    {
        $convalidacio = Convalidacio::query()->with('sollicitud.alumno')->findOrFail($convalidacioId);

        if (blank($convalidacio->certificat_path)) {
            throw new ConvalidacioException('No hi ha cap document adjunt.');
        }

        if (!Storage::disk('private')->exists($convalidacio->certificat_path)) {
            throw new ConvalidacioException('El document no s\'ha trobat al servidor.');
        }

        return Storage::disk('private')->path($convalidacio->certificat_path);
    }

    /**
     * Guarda un certificat adjunt i torna la ruta on s'ha desat.
     */
    public function guardarCertificat(\SplFileInfo $file, string $alumnoId): string
    {
        $nomFitxer = sprintf(
            '%s_%s_%s',
            now()->timestamp,
            preg_replace('/[^A-Za-z0-9_]/', '_', $alumnoId),
            preg_replace('/[^A-Za-z0-9_]/', '_', $file->getClientOriginalName())
        );

        $ruta = sprintf('%s/%s', self::CERTIFICATS_PATH, $alumnoId);

        $file->move(Storage::disk('private')->path($ruta), $nomFitxer);

        return sprintf('%s/%s', $ruta, $nomFitxer);
    }

    /**
     * Valida que el fitxer adjunt siga un PDF o una imatge.
     */
    public function validarTipusFitxer(\SplFileInfo $file): bool
    {
        $mime = $file->getMimeType();

        return in_array($mime, [
            'application/pdf',
            'image/jpeg',
            'image/png',
            'image/jpg',
        ]);
    }
}
