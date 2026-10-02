<?php

declare(strict_types=1);

namespace Intranet\Application\Convalidacio;

use Illuminate\Database\QueryException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intranet\Entities\Alumno;
use Intranet\Entities\Convalidacio;
use Intranet\Entities\DocumentConvalidacio;
use Intranet\Entities\ModulFolLogse;
use Intranet\Entities\Profesor;
use Intranet\Entities\SollicitudConvalidacio;

/** Casos d'ús del cicle de vida de les convalidacions. */
class ConvalidacioService
{
    private const MODUL_IPE_I = '1709';

    public function __construct(private readonly ResultatsAcademicsXmlService $resultatsAcademics)
    {
    }

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

        $storedPaths = [];

        try {
            return DB::transaction(function () use ($alumno, $token, $items, &$storedPaths): SollicitudConvalidacio {
                Alumno::query()->whereKey($alumno->nia)->lockForUpdate()->firstOrFail();

                $existent = SollicitudConvalidacio::query()
                    ->where('alumno_id', $alumno->nia)
                    ->where('submission_token', $token)
                    ->first();
                if ($existent) {
                    return $existent->load('convalidacions');
                }

                $items = $this->validarComposicio($alumno, $items);

                $sollicitud = SollicitudConvalidacio::query()->create([
                    'alumno_id' => $alumno->nia,
                    'submission_token' => $token,
                    'submitted_at' => now(),
                ]);

                foreach ($items as $item) {
                    $peticio = $sollicitud->convalidacions()->create([
                        'modulo_destino_id' => $item['modulo_destino_id'],
                        'origen' => $item['origen'],
                        'modulo_origen_codigo' => $item['modulo_origen_codigo'] ?? null,
                        'modulo_origen_nombre' => $item['modulo_origen_nombre'] ?? null,
                        'modulo_origen_nombre_val' => $item['modulo_origen_nombre_val'] ?? null,
                        'modulo_origen_nombre_cas' => $item['modulo_origen_nombre_cas'] ?? null,
                        'ciclo_origen_codigo' => $item['ciclo_origen_codigo'] ?? null,
                        'ciclo_origen_nombre' => $item['ciclo_origen_nombre'] ?? null,
                        'ciclo_origen_nombre_val' => $item['ciclo_origen_nombre_val'] ?? null,
                        'ciclo_origen_nombre_cas' => $item['ciclo_origen_nombre_cas'] ?? null,
                        'familia_professional_codigo' => $item['familia_professional_codigo'] ?? null,
                        'familia_professional_nombre_val' => $item['familia_professional_nombre_val'] ?? null,
                        'familia_professional_nombre_cas' => $item['familia_professional_nombre_cas'] ?? null,
                        'ciclo_matricula_id' => $item['ciclo_matricula_id'],
                        'ciclo_matricula_codigo' => $item['ciclo_matricula_codigo'],
                        'ciclo_matricula_nombre_val' => $item['ciclo_matricula_nombre_val'],
                        'ciclo_matricula_nombre_cas' => $item['ciclo_matricula_nombre_cas'],
                        'departamento_matricula_id' => $item['departamento_matricula_id'],
                        'familia_matricula_nombre_val' => $item['familia_matricula_nombre_val'],
                        'familia_matricula_nombre_cas' => $item['familia_matricula_nombre_cas'],
                        'familia_matricula_codigo_xml' => $item['familia_matricula_codigo_xml'],
                        'familia_matricula_abreviatura_xml' => $item['familia_matricula_abreviatura_xml'],
                        'ciclo_matricula_tipo' => $item['ciclo_matricula_tipo'],
                        'ciclo_matricula_tipo_nombre_val' => $item['ciclo_matricula_tipo_nombre_val'],
                        'ciclo_matricula_tipo_nombre_cas' => $item['ciclo_matricula_tipo_nombre_cas'],
                        'ciclo_matricula_normativa' => $item['ciclo_matricula_normativa'],
                        'nivel_origen_codigo' => $item['nivel_origen_codigo'] ?? null,
                        'nivel_origen_nombre_val' => $item['nivel_origen_nombre_val'] ?? null,
                        'nivel_origen_nombre_cas' => $item['nivel_origen_nombre_cas'] ?? null,
                        'any_origen' => $item['any_origen'] ?? null,
                        'nota_origen' => $item['nota_origen'] ?? null,
                        'convocatoria_origen' => $item['convocatoria_origen'] ?? null,
                        'declaracio_responsable' => (bool) ($item['declaracio_responsable'] ?? false),
                        'fol_logse' => $item['fol_logse'] ?? null,
                        'modulo_origen_es_fol' => $item['modulo_origen_es_fol'] ?? null,
                        'fol_logse_cicle' => $item['fol_logse_cicle'] ?? null,
                        'fol_logse_nivell' => $item['fol_logse_nivell'] ?? null,
                        'estat' => Convalidacio::ESTAT_EN_PROCES,
                    ]);
                    foreach ($item['documents'] ?? [] as $adjunt) {
                        if (!($adjunt['fitxer'] ?? null) instanceof UploadedFile) {
                            continue;
                        }
                        $saved = $this->guardarAdjunt($alumno, $sollicitud, $adjunt['fitxer']);
                        $storedPaths[] = $saved['path'];
                        $descripcio = trim((string) $adjunt['descripcio']);
                        $peticio->documents()->create(['descripcio' => $descripcio, ...$saved]);
                        if (preg_match('/prl|prevenci[oó]n?.*riesgos/i', $descripcio)) {
                            $peticio->forceFill([
                                'document_prl_path' => $saved['path'],
                                'document_prl_original_name' => $saved['original_name'],
                                'document_prl_mime' => $saved['mime'],
                            ])->save();
                        } elseif (!$peticio->document_path) {
                            $peticio->forceFill([
                                'document_path' => $saved['path'],
                                'document_original_name' => $saved['original_name'],
                                'document_mime' => $saved['mime'],
                            ])->save();
                        }
                    }
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

    /** Elimina una sol·licitud de prova i els documents privats associats. */
    public function eliminarSollicitud(SollicitudConvalidacio $sollicitud): void
    {
        $documents = DB::transaction(function () use ($sollicitud): array {
            $actual = SollicitudConvalidacio::query()
                ->with(['convalidacions:id,sollicitud_convalidacio_id,document_path,document_prl_path', 'convalidacions.documents:id,convalidacio_id,path'])
                ->lockForUpdate()
                ->findOrFail($sollicitud->id);

            $paths = $actual->convalidacions
                ->flatMap(fn (Convalidacio $peticio): array => [
                    $peticio->document_path, $peticio->document_prl_path,
                    ...$peticio->documents->pluck('path')->all(),
                ])
                ->filter()
                ->unique()
                ->values()
                ->all();

            $actual->convalidacions()->delete();
            $actual->delete();

            return $paths;
        });

        if ($documents !== [] && !Storage::disk('convalidacions')->delete($documents)) {
            throw new ConvalidacioException('La sol·licitud s\'ha eliminat, però no s\'han pogut eliminar tots els documents privats.');
        }
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

    /** Substituïx només el fitxer triat i conserva la descripció de l'adjunt. */
    public function corregirAdjunt(Convalidacio $peticio, DocumentConvalidacio $document, Alumno $alumno, UploadedFile $file): Convalidacio
    {
        if ((int) $document->convalidacio_id !== (int) $peticio->id
            || (string) $peticio->sollicitud->alumno_id !== (string) $alumno->nia
            || !$peticio->esOrigenExtern()
            || $peticio->estat !== Convalidacio::ESTAT_REVISAR_DOCUMENTACIO) {
            throw new ConvalidacioException('Esta petició no admet una correcció d’este document.');
        }

        $saved = $this->guardarAdjunt($alumno, $peticio->sollicitud, $file);
        $oldPath = $document->path;
        try {
            DB::transaction(function () use ($peticio, $document, $saved, $oldPath): void {
                $actual = Convalidacio::query()->lockForUpdate()->findOrFail($peticio->id);
                if ($actual->estat !== Convalidacio::ESTAT_REVISAR_DOCUMENTACIO) {
                    throw new ConvalidacioException('Esta petició ja no admet una correcció documental.');
                }
                $documentActual = DocumentConvalidacio::query()
                    ->where('convalidacio_id', $actual->id)
                    ->lockForUpdate()
                    ->findOrFail($document->id);
                $documentActual->forceFill($saved)->save();
                $canvis = ['estat' => Convalidacio::ESTAT_EN_PROCES];
                if ($actual->document_path === $oldPath) {
                    $canvis += [
                        'document_path' => $saved['path'],
                        'document_original_name' => $saved['original_name'],
                        'document_mime' => $saved['mime'],
                    ];
                }
                if ($actual->document_prl_path === $oldPath) {
                    $canvis += [
                        'document_prl_path' => $saved['path'],
                        'document_prl_original_name' => $saved['original_name'],
                        'document_prl_mime' => $saved['mime'],
                    ];
                }
                $actual->forceFill($canvis)->save();
            });
        } catch (\Throwable $exception) {
            Storage::disk('convalidacions')->delete($saved['path']);
            throw $exception;
        }
        Storage::disk('convalidacions')->delete($oldPath);

        return $peticio->fresh();
    }

    /**
     * @param array<int, array<string, mixed>> $items
     * @return array<int, array<string, mixed>>
     */
    private function validarComposicio(Alumno $alumno, array $items): array
    {
        if ($items === []) {
            throw new ConvalidacioException('Has d\'afegir almenys un mòdul.');
        }

        $destins = array_column($items, 'modulo_destino_id');
        if (count($destins) !== count(array_unique($destins))) {
            throw new ConvalidacioException('No pots repetir el mateix mòdul destí.');
        }

        foreach ($items as $index => $item) {
            $items[$index] = $this->validarItem($alumno, $item);
        }

        return $items;
    }

    /**
     * @param array<string, mixed> $item
     * @return array<string, mixed>
     */
    private function validarItem(Alumno $alumno, array $item): array
    {
        if (empty($item['documents'])) {
            $legacyDocs = [];
            if (($item['document'] ?? null) instanceof UploadedFile) {
                $legacyDocs[] = ['descripcio' => 'Certificat acadèmic', 'fitxer' => $item['document']];
            }
            if (($item['document_prl'] ?? null) instanceof UploadedFile) {
                $legacyDocs[] = ['descripcio' => 'Certificat PRL', 'fitxer' => $item['document_prl']];
            }
            $item['documents'] = $legacyDocs;
        }
        $destino = (string) ($item['modulo_destino_id'] ?? '');
        $origen = (string) ($item['origen'] ?? '');

        if (!array_key_exists($origen, Convalidacio::origenOptions())) {
            throw new ConvalidacioException('L\'origen indicat no és vàlid.');
        }
        $esIpeI = $destino === self::MODUL_IPE_I;
        if ($esIpeI && !is_bool($item['fol_logse'] ?? null)) {
            throw new ConvalidacioException('Revisa el certificat acadèmic i indica si els estudis d’origen són LOGSE.');
        }
        $folLogse = $esIpeI ? $item['fol_logse'] : null;

        $grups = $alumno->Grupo()->pluck('grupos.codigo');
        $destinoValido = DB::table('modulo_grupos')
            ->join('modulo_ciclos', 'modulo_ciclos.id', '=', 'modulo_grupos.idModuloCiclo')
            ->whereIn('modulo_grupos.idGrupo', $grups)
            ->where('modulo_ciclos.idModulo', $destino)
            ->exists();

        if (!$destinoValido) {
            throw new ConvalidacioException('El mòdul destí no pertany a la matrícula vigent.');
        }

        $peticioOberta = Convalidacio::query()
            ->whereHas('sollicitud', fn ($query) => $query->where('alumno_id', $alumno->nia))
            ->where('modulo_destino_id', $destino)
            ->where('estat', '!=', Convalidacio::ESTAT_DENEGADA)
            ->exists();

        if ($peticioOberta) {
            throw new ConvalidacioException('Ja tens una petició oberta o resolta favorablement per a este mòdul.');
        }

        $contextMatricula = $this->contextMatricula($alumno, $destino);

        if ($origen === Convalidacio::ORIGEN_PROPI_CENTRE) {
            $resultat = $this->resultatsAcademics->trobarAprovat(
                (string) $alumno->nia,
                (string) ($item['resultat_origen_id'] ?? '')
            );
            if ($resultat === null) {
                throw new ConvalidacioException('El mòdul superat no consta en els resultats acadèmics disponibles.');
            }
            if (blank($resultat['familia_professional']) || blank($resultat['nivell_formatiu_origen'])) {
                throw new ConvalidacioException('No s\'ha pogut identificar el nivell formatiu o la família professional del mòdul superat.');
            }

            $catalog = $esIpeI ? ModulFolLogse::query()->find((string) $resultat['modul']) : null;
            $esFol = $catalog !== null;
            $documents = $this->validarDocuments($item, false);
            $esIPEIFol = $destino === self::MODUL_IPE_I && $esFol && $folLogse === true;
            $this->validarDocumentPrl($documents, $esIPEIFol, false);

            return array_merge($item, $contextMatricula, [
                'modulo_origen_codigo' => $resultat['modul'],
                'modulo_origen_nombre' => $resultat['nom_modul'],
                'modulo_origen_nombre_val' => $resultat['nom_modul_val'],
                'modulo_origen_nombre_cas' => $resultat['nom_modul_cas'],
                'ciclo_origen_codigo' => $resultat['cicle'],
                'ciclo_origen_nombre' => $resultat['nom_cicle'],
                'ciclo_origen_nombre_val' => $resultat['nom_cicle_val'],
                'ciclo_origen_nombre_cas' => $resultat['nom_cicle_cas'],
                'familia_professional_codigo' => $resultat['familia_professional'],
                'familia_professional_nombre_val' => $resultat['familia_professional_val'],
                'familia_professional_nombre_cas' => $resultat['familia_professional_cas'],
                'nivel_origen_codigo' => $resultat['nivell_formatiu_origen'],
                'nivel_origen_nombre_val' => $resultat['nivell_formatiu_origen_val'],
                'nivel_origen_nombre_cas' => $resultat['nivell_formatiu_origen_cas'],
                'any_origen' => $resultat['any'],
                'nota_origen' => $resultat['nota'],
                'convocatoria_origen' => $resultat['convocatoria'],
                'document' => null,
                'declaracio_responsable' => false,
                'fol_logse' => $folLogse,
                'modulo_origen_es_fol' => $esIpeI ? $esFol : null,
                'fol_logse_cicle' => $catalog?->cicle,
                'fol_logse_nivell' => $catalog?->nivell,
                'documents' => $documents,
            ]);
        }

        $esIPEIAltresEstudis = $destino === self::MODUL_IPE_I && $origen === Convalidacio::ORIGEN_ALTRE_CENTRE;
        if ($esIPEIAltresEstudis && !is_bool($item['modulo_origen_es_fol'] ?? null)) {
            throw new ConvalidacioException('Indica si l’origen de la petició és el mòdul FOL.');
        }
        if ($origen !== Convalidacio::ORIGEN_PROPI_CENTRE && ($item['declaracio_responsable'] ?? false) !== true) {
            throw new ConvalidacioException('Accepta la declaració responsable per als documents adjunts.');
        }
        $documents = $this->validarDocuments($item, true);
        if ($origen === Convalidacio::ORIGEN_ALTRE_CENTRE && !$this->teDocumentAcademic($documents)) {
            throw new ConvalidacioException('Identifica almenys un document com a certificat acadèmic o expedient.');
        }
        $esFol = $origen === Convalidacio::ORIGEN_ALTRE_CENTRE && ($item['modulo_origen_es_fol'] ?? false);
        $esIPEIFol = $esIPEIAltresEstudis && $esFol && $folLogse === true;
        $this->validarDocumentPrl($documents, $esIPEIFol, $origen === Convalidacio::ORIGEN_ALTRE_CENTRE);

        return array_merge($item, $contextMatricula, [
            'fol_logse' => $folLogse,
            'modulo_origen_es_fol' => $esIPEIAltresEstudis ? $esFol : null,
            'documents' => $documents,
        ]);
    }

    /**
     * Resol el context vigent del cicle i la família del mòdul destí des de la matrícula.
     *
     * @return array{ciclo_matricula_id:int, ciclo_matricula_codigo:string, ciclo_matricula_nombre_val:string|null, ciclo_matricula_nombre_cas:string|null, departamento_matricula_id:int, familia_matricula_nombre_val:string, familia_matricula_nombre_cas:string, familia_matricula_codigo_xml:string, familia_matricula_abreviatura_xml:string, ciclo_matricula_tipo:int, ciclo_matricula_tipo_nombre_val:string|null, ciclo_matricula_tipo_nombre_cas:string|null, ciclo_matricula_normativa:string}
     */
    private function contextMatricula(Alumno $alumno, string $destino): array
    {
        $grups = $alumno->Grupo()->pluck('grupos.codigo');
        $cicles = DB::table('modulo_grupos')
            ->join('modulo_ciclos', 'modulo_ciclos.id', '=', 'modulo_grupos.idModuloCiclo')
            ->join('ciclos', 'ciclos.id', '=', 'modulo_ciclos.idCiclo')
            ->leftJoin('departamentos', 'departamentos.id', '=', 'ciclos.departamento')
            ->whereIn('modulo_grupos.idGrupo', $grups)
            ->where('modulo_ciclos.idModulo', $destino)
            ->select([
                'ciclos.id as ciclo_id',
                'ciclos.ciclo as ciclo_codigo',
                'ciclos.vliteral as ciclo_nombre_val',
                'ciclos.cliteral as ciclo_nombre_cas',
                'ciclos.tipo as ciclo_tipo',
                'ciclos.normativa as ciclo_normativa',
                'departamentos.id as departamento_id',
                'departamentos.familia_professional_val',
                'departamentos.familia_professional_cas',
                'departamentos.codigo_xml',
                'departamentos.abreviatura_xml',
            ])
            ->distinct()
            ->get()
            ->unique('ciclo_id')
            ->values();

        if ($cicles->count() !== 1) {
            throw new ConvalidacioException("No s'ha pogut identificar un únic cicle de matrícula per al mòdul destí.");
        }

        $cicle = $cicles->first();
        if (
            $cicle->departamento_id === null
            || blank($cicle->familia_professional_val)
            || blank($cicle->familia_professional_cas)
            || blank($cicle->codigo_xml)
            || blank($cicle->abreviatura_xml)
        ) {
            throw new ConvalidacioException("No s'ha pogut identificar la família professional del cicle de matrícula.");
        }

        return [
            'ciclo_matricula_id' => (int) $cicle->ciclo_id,
            'ciclo_matricula_codigo' => $cicle->ciclo_codigo,
            'ciclo_matricula_nombre_val' => $cicle->ciclo_nombre_val,
            'ciclo_matricula_nombre_cas' => $cicle->ciclo_nombre_cas,
            'departamento_matricula_id' => (int) $cicle->departamento_id,
            'familia_matricula_nombre_val' => $cicle->familia_professional_val,
            'familia_matricula_nombre_cas' => $cicle->familia_professional_cas,
            'familia_matricula_codigo_xml' => $cicle->codigo_xml,
            'familia_matricula_abreviatura_xml' => $cicle->abreviatura_xml,
            'ciclo_matricula_tipo' => (int) $cicle->ciclo_tipo,
            'ciclo_matricula_tipo_nombre_val' => config('auxiliares.tipoEstudio.' . $cicle->ciclo_tipo),
            'ciclo_matricula_tipo_nombre_cas' => config('auxiliares.tipoEstudioC.' . $cicle->ciclo_tipo),
            'ciclo_matricula_normativa' => $cicle->ciclo_normativa,
        ];
    }

    /** Guarda un adjunt amb nom opac en l'emmagatzematge privat. */
    private function guardarAdjunt(Alumno $alumno, SollicitudConvalidacio $sollicitud, UploadedFile $file): array
    {
        $this->validarDocument($file);
        $name = Str::uuid() . '.' . strtolower($file->getClientOriginalExtension());
        $path = $file->storeAs($alumno->nia . '/' . $sollicitud->id, $name, 'convalidacions');
        if (!$path) {
            throw new ConvalidacioException('No s\'ha pogut guardar el document.');
        }

        return [
            'path' => $path,
            'original_name' => mb_substr($file->getClientOriginalName(), 0, 255),
            'mime' => $file->getMimeType(),
        ];
    }

    /** Guarda fitxers per a les rutes de correcció llegades. */
    private function guardarDocument(Alumno $alumno, SollicitudConvalidacio $sollicitud, ?UploadedFile $file): array
    {
        if (!$file) {
            return ['document_path' => null, 'document_original_name' => null, 'document_mime' => null];
        }
        $saved = $this->guardarAdjunt($alumno, $sollicitud, $file);

        return [
            'document_path' => $saved['path'],
            'document_original_name' => $saved['original_name'],
            'document_mime' => $saved['mime'],
        ];
    }

    /** Comprova nombre, descripcions i fitxers abans d'escriure la petició. */
    private function validarDocuments(array $item, bool $obligatoris): array
    {
        $documents = array_values($item['documents'] ?? []);
        if (count($documents) > 3 || ($obligatoris && $documents === [])) {
            throw new ConvalidacioException($obligatoris
                ? 'Adjunta almenys un document (màxim tres).'
                : 'Pots adjuntar un màxim de tres documents.');
        }
        foreach ($documents as $document) {
            if (!($document['fitxer'] ?? null) instanceof UploadedFile || blank($document['descripcio'] ?? null)) {
                throw new ConvalidacioException('Cada document adjunt necessita un fitxer i una descripció.');
            }
            $this->validarDocument($document['fitxer']);
        }

        return $documents;
    }

    /** Exigix evidència acadèmica i PRL separades per al cas FOL LOGSE → IPE I. */
    private function validarDocumentPrl(array $documents, bool $requereixPrl, bool $extern): void
    {
        if (!$requereixPrl) {
            return;
        }
        $prl = array_filter($documents, static fn (array $document): bool =>
            preg_match('/prevenci[oó]n?.*riesgos|prl/u', mb_strtolower((string) ($document['descripcio'] ?? ''))) === 1
        );
        $academics = array_filter($documents, static fn (array $document): bool =>
            preg_match('/certificat.*(acad[eè]mic|notes|estudis)|expedient/u', mb_strtolower((string) ($document['descripcio'] ?? ''))) === 1
        );
        if ($prl === [] || ($extern && $academics === [])) {
            throw new ConvalidacioException($extern
                ? 'Adjunta per separat el certificat acadèmic i el certificat PRL.'
                : 'Adjunta el certificat de Prevenció de Riscos Laborals.');
        }
    }

    /** Indica si hi ha un adjunt identificat com a document acadèmic. */
    private function teDocumentAcademic(array $documents): bool
    {
        foreach ($documents as $document) {
            if (preg_match('/certificat.*(acad[eè]mic|notes|estudis)|expedient/u', mb_strtolower((string) ($document['descripcio'] ?? '')))) {
                return true;
            }
        }

        return false;
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
