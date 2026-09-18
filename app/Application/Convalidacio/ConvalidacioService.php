<?php

declare(strict_types=1);

namespace Intranet\Application\Convalidacio;

use Illuminate\Database\QueryException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intranet\Entities\Alumno;
use Intranet\Entities\AlumnoResultado;
use Intranet\Entities\Convalidacio;
use Intranet\Entities\Profesor;
use Intranet\Entities\SollicitudConvalidacio;

/** Casos d'ús del cicle de vida de les convalidacions. */
class ConvalidacioService
{
    /**
     * Tramita de manera atòmica i idempotent una composició validada.
     *
     * @param array<int, array<string, mixed>> $items
     */
    public function tramitar(Alumno $alumno, string $token, array $items): SollicitudConvalidacio
    {
        $existent = SollicitudConvalidacio::query()
            ->where('alumno_id', $alumno->nia)
            ->where('submission_token', $token)
            ->first();

        if ($existent) {
            return $existent->load('convalidacions');
        }

        $this->validarComposicio($alumno, $items);
        $storedPaths = [];

        try {
            return DB::transaction(function () use ($alumno, $token, $items, &$storedPaths): SollicitudConvalidacio {
                $sollicitud = SollicitudConvalidacio::query()->create([
                    'alumno_id' => $alumno->nia,
                    'submission_token' => $token,
                    'submitted_at' => now(),
                ]);

                foreach ($items as $item) {
                    $document = $this->guardarDocument($alumno, $sollicitud, $item['document'] ?? null);
                    if ($document['document_path']) {
                        $storedPaths[] = $document['document_path'];
                    }

                    $sollicitud->convalidacions()->create(array_merge([
                        'modulo_destino_id' => $item['modulo_destino_id'],
                        'origen' => $item['origen'],
                        'modulo_origen_id' => $item['modulo_origen_id'] ?? null,
                        'declaracio_responsable' => (bool) ($item['declaracio_responsable'] ?? false),
                        'estat' => Convalidacio::ESTAT_EN_PROCES,
                    ], $document));
                }

                return $sollicitud->load('convalidacions');
            }, 3);
        } catch (QueryException $exception) {
            Storage::disk('convalidacions')->delete($storedPaths);
            $existent = SollicitudConvalidacio::query()
                ->where('alumno_id', $alumno->nia)
                ->where('submission_token', $token)
                ->first();

            if ($existent) {
                return $existent->load('convalidacions');
            }

            throw $exception;
        } catch (\Throwable $exception) {
            Storage::disk('convalidacions')->delete($storedPaths);
            throw $exception;
        }
    }

    /** Canvia l'estat d'una sola petició i conserva la traçabilitat. */
    public function revisar(Convalidacio $peticio, Profesor $revisor, string $estat, ?string $observacions): Convalidacio
    {
        if (!array_key_exists($estat, Convalidacio::estatOptions())) {
            throw new ConvalidacioException('L\'estat indicat no és vàlid.');
        }

        $requereixObservacio = in_array($estat, [
            Convalidacio::ESTAT_DENEGADA,
            Convalidacio::ESTAT_REVISAR_DOCUMENTACIO,
            Convalidacio::ESTAT_APORTAR_ORIGINAL,
        ], true);

        if ($requereixObservacio && blank($observacions)) {
            throw new ConvalidacioException('L\'observació és obligatòria per a l\'estat seleccionat.');
        }

        return DB::transaction(function () use ($peticio, $revisor, $estat, $observacions): Convalidacio {
            $actual = Convalidacio::query()->lockForUpdate()->findOrFail($peticio->id);
            if ($actual->esTerminal()) {
                throw new ConvalidacioException('Una petició realitzada és de només consulta.');
            }

            $actual->forceFill([
                'estat' => $estat,
                'observacions' => filled($observacions) ? trim((string) $observacions) : null,
                'revisat_per' => $revisor->dni,
                'revisat_at' => now(),
            ])->save();

            return $actual->fresh();
        });
    }

    /** Substituïx exclusivament el document requerit i reactiva la petició. */
    public function corregirDocument(Convalidacio $peticio, Alumno $alumno, UploadedFile $file): Convalidacio
    {
        if ((string) $peticio->sollicitud->alumno_id !== (string) $alumno->nia) {
            throw new ConvalidacioException('No pots modificar esta petició.');
        }

        if (!$peticio->esOrigenExtern() || $peticio->estat !== Convalidacio::ESTAT_REVISAR_DOCUMENTACIO) {
            throw new ConvalidacioException('Esta petició no admet una correcció documental.');
        }

        $this->validarDocument($file);
        $document = $this->guardarDocument($alumno, $peticio->sollicitud, $file);
        $anterior = $peticio->document_path;

        try {
            DB::transaction(function () use ($peticio, $document): void {
                $actual = Convalidacio::query()->lockForUpdate()->findOrFail($peticio->id);
                if ($actual->estat !== Convalidacio::ESTAT_REVISAR_DOCUMENTACIO || !$actual->esOrigenExtern()) {
                    throw new ConvalidacioException('Esta petició ja no admet una correcció documental.');
                }
                $actual->forceFill(array_merge($document, ['estat' => Convalidacio::ESTAT_EN_PROCES]))->save();
            });
        } catch (\Throwable $exception) {
            Storage::disk('convalidacions')->delete($document['document_path']);
            throw $exception;
        }

        if ($anterior) {
            Storage::disk('convalidacions')->delete($anterior);
        }

        return $peticio->fresh();
    }

    /** @param array<int, array<string, mixed>> $items */
    private function validarComposicio(Alumno $alumno, array $items): void
    {
        if ($items === []) {
            throw new ConvalidacioException('Has d\'afegir almenys un mòdul.');
        }

        $destins = array_column($items, 'modulo_destino_id');
        if (count($destins) !== count(array_unique($destins))) {
            throw new ConvalidacioException('No pots repetir el mateix mòdul destí.');
        }

        foreach ($items as $item) {
            $this->validarItem($alumno, $item);
        }
    }

    /** @param array<string, mixed> $item */
    private function validarItem(Alumno $alumno, array $item): void
    {
        $destino = (string) ($item['modulo_destino_id'] ?? '');
        $origen = (string) ($item['origen'] ?? '');

        if (!array_key_exists($origen, Convalidacio::origenOptions())) {
            throw new ConvalidacioException('L\'origen indicat no és vàlid.');
        }

        $grups = $alumno->Grupo()->pluck('grupos.codigo');
        $destinoValido = DB::table('modulo_grupos')
            ->join('modulo_ciclos', 'modulo_ciclos.id', '=', 'modulo_grupos.idModuloCiclo')
            ->whereIn('modulo_grupos.idGrupo', $grups)
            ->where('modulo_ciclos.idModulo', $destino)
            ->exists();

        if (!$destinoValido) {
            throw new ConvalidacioException('El mòdul destí no pertany a la matrícula vigent.');
        }

        if ($origen === Convalidacio::ORIGEN_PROPI_CENTRE) {
            $moduloOrigen = (string) ($item['modulo_origen_id'] ?? '');
            $origenValido = AlumnoResultado::query()
                ->where('idAlumno', $alumno->nia)
                ->whereHas('ModuloGrupo.ModuloCiclo', fn ($query) => $query->where('idModulo', $moduloOrigen))
                ->exists();

            if (!$origenValido) {
                throw new ConvalidacioException('El mòdul origen no consta en l\'historial de l\'alumne.');
            }

            return;
        }

        if (($item['declaracio_responsable'] ?? false) !== true || !($item['document'] ?? null) instanceof UploadedFile) {
            throw new ConvalidacioException('Els orígens externs requerixen declaració responsable i un document.');
        }

        $this->validarDocument($item['document']);
    }

    /** @return array{document_path: ?string, document_original_name: ?string, document_mime: ?string} */
    private function guardarDocument(Alumno $alumno, SollicitudConvalidacio $sollicitud, ?UploadedFile $file): array
    {
        if (!$file) {
            return ['document_path' => null, 'document_original_name' => null, 'document_mime' => null];
        }

        $name = Str::uuid() . '.' . strtolower($file->getClientOriginalExtension());
        $path = $file->storeAs($alumno->nia . '/' . $sollicitud->id, $name, 'convalidacions');

        if (!$path) {
            throw new ConvalidacioException('No s\'ha pogut guardar el document.');
        }

        return [
            'document_path' => $path,
            'document_original_name' => mb_substr($file->getClientOriginalName(), 0, 255),
            'document_mime' => $file->getMimeType(),
        ];
    }

    /** Valida el document també en la frontera de domini. */
    private function validarDocument(UploadedFile $file): void
    {
        $extensions = ['pdf', 'jpg', 'jpeg', 'png'];
        $mimes = ['application/pdf', 'image/jpeg', 'image/png'];
        $maxBytes = (int) config('convalidacions.max_document_kb', 5120) * 1024;

        if (!$file->isValid()
            || !in_array(strtolower($file->getClientOriginalExtension()), $extensions, true)
            || !in_array((string) $file->getMimeType(), $mimes, true)
            || $file->getSize() > $maxBytes) {
            throw new ConvalidacioException('El document ha de ser PDF, JPG, JPEG o PNG i respectar el límit de mida.');
        }
    }
}
