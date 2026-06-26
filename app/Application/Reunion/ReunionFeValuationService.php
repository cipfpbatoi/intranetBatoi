<?php

declare(strict_types=1);

namespace Intranet\Application\Reunion;

use Illuminate\Support\Collection;
use Intranet\Application\AlumnoFct\AlumnoFctAvalService;
use Intranet\Entities\OrdenReunion;
use Intranet\Entities\AlumnoFct;
use Intranet\Entities\AlumnoResultado;
use Intranet\Entities\Grupo;
use Intranet\Entities\Modulo_grupo;
use Intranet\Entities\Reunion;

/**
 * Servei per garantir el punt editable de valoració FE en actes d'avaluació LFP.
 */
class ReunionFeValuationService
{
    public const ORDER_DESCRIPTION = 'Valoració de la FE';
    public const NOTES_ORDER_DESCRIPTION = 'Notes Formacio en Centre dels mòduls de l\'alumnat no apte, amb cessament o amb renúncia';
    private const LEGACY_NOTES_ORDER_DESCRIPTION = 'Notes reals dels mòduls de l\'alumnat no apte o amb cessament';
    private const LEGACY_NOTES_ORDER_DESCRIPTION_WITH_RENUNCIA = 'Notes reals dels mòduls de l\'alumnat no apte, amb cessament o amb renúncia';
    private const VALID_REAL_GRADES = [0, 5, 6, 7, 8, 9, 10];
    private const DEPRECATED_SECOND_YEAR_INSTRUCTION = '<p><strong>Grups de 2n:</strong> indiqueu les notes reals '
        . 'dels mòduls de l\'alumnat no apte o que no ha realitzat la FE quan siga necessari.</p>';
    private const DEPRECATED_SECRETARY_INSTRUCTION = '<p>El tutor o tutora ha d\'anar a secretaria per a anul·lar '
        . 'la convocatòria extraordinària quan corresponga.</p>';

    private ?AlumnoFctAvalService $alumnoFctAvalService;

    public function __construct(?AlumnoFctAvalService $alumnoFctAvalService = null)
    {
        $this->alumnoFctAvalService = $alumnoFctAvalService;
    }

    /**
     * Garantix que l'acta final o extraordinària LFP tinga un punt editable de valoració FE.
     *
     * @param Reunion $reunion
     * @param string|null $normativa Normativa ja resolta, si el cridador la té disponible.
     * @return OrdenReunion|null
     */
    public function ensureOrder(Reunion $reunion, ?string $normativa = null): ?OrdenReunion
    {
        if (!$this->needsFeOrder($reunion, $normativa)) {
            $this->removeFeOrders($reunion);

            return null;
        }

        $existing = OrdenReunion::query()
            ->forReunion($reunion->id)
            ->where('descripcion', self::ORDER_DESCRIPTION)
            ->first();

        if ($existing) {
            $this->removeDeprecatedSecondYearInstruction($existing);
            $this->removeDeprecatedSecretaryInstruction($existing);
            $this->refreshNotesOrder($reunion, true);

            return $existing;
        }

        $nextOrder = ((int) OrdenReunion::query()
            ->forReunion($reunion->id)
            ->max('orden')) + 1;

        $order = OrdenReunion::create([
            'idReunion' => $reunion->id,
            'orden' => $nextOrder,
            'descripcion' => self::ORDER_DESCRIPTION,
            'resumen' => $this->defaultSummaryForReunion($reunion),
        ]);

        $this->refreshNotesOrder($reunion);

        return $order;
    }

    /**
     * Indica si una reunió necessita el punt FE.
     *
     * @param Reunion $reunion
     * @param string|null $normativa
     * @return bool
     */
    public function needsFeOrder(Reunion $reunion, ?string $normativa = null): bool
    {
        $resolvedNormativa = $this->resolveActaNormativa($reunion, $normativa);

        return (int) $reunion->tipo === 7
            && in_array((int) $reunion->numero, [34, 35], true)
            && $resolvedNormativa === 'LFP';
    }

    /**
     * Resol la normativa efectiva de l'acta mantenint el comportament antic si no es pot deduir.
     *
     * @param Reunion $reunion
     * @param string|null $normativa
     * @return string
     */
    private function resolveActaNormativa(Reunion $reunion, ?string $normativa = null): string
    {
        $resolvedNormativa = $normativa === null ? (string) $reunion->normativa : $normativa;
        $resolvedNormativa = strtoupper(trim((string) $resolvedNormativa));

        return $resolvedNormativa === '' ? 'LFP' : $resolvedNormativa;
    }

    /**
     * Elimina els punts automàtics de FE quan l'acta no els necessita.
     *
     * @param Reunion $reunion
     * @return void
     */
    private function removeFeOrders(Reunion $reunion): void
    {
        OrdenReunion::query()
            ->forReunion($reunion->id)
            ->whereIn('descripcion', [
                self::ORDER_DESCRIPTION,
                self::NOTES_ORDER_DESCRIPTION,
                self::LEGACY_NOTES_ORDER_DESCRIPTION,
                self::LEGACY_NOTES_ORDER_DESCRIPTION_WITH_RENUNCIA,
            ])
            ->delete();
    }

    /**
     * Plantilla inicial perquè el tutor complete manualment la valoració FE.
     *
     * @return string
     */
    public function defaultSummary(): string
    {
        return $this->summaryWithKnownData([]);
    }

    /**
     * Plantilla inicial per a una acta concreta, amb dades conegudes pel grup o tutor.
     *
     * @param Reunion $reunion
     * @return string
     */
    public function defaultSummaryForReunion(Reunion $reunion): string
    {
        return $this->summaryWithKnownData($this->knownSectionsForReunion($reunion));
    }

    /**
     * Combina les dades conegudes amb els apartats manuals que ha d'emplenar el tutor.
     *
     * @param array<string, array<int, string>> $knownSections
     * @return string
     */
    private function summaryWithKnownData(array $knownSections): string
    {
        $lines = [
            $this->sectionLine(
                'Alumnat apte',
                $knownSections['aptes'] ?? [],
                'indiqueu l\'alumnat i les hores realitzades.'
            ),
            $this->sectionLine(
                'Alumnat no apte',
                $knownSections['no_aptes'] ?? [],
                'indiqueu l\'alumnat i les hores realitzades.'
            ),
            $this->sectionLine(
                'Alumnat convalidat/exempt',
                $knownSections['convalidats'] ?? [],
                'indiqueu l\'alumnat corresponent.'
            ),
            $this->sectionLine(
                'Alumnat en cessament',
                $knownSections['cessaments'] ?? [],
                'indiqueu l\'alumnat i la justificació.'
            ),
            $this->sectionLine(
                'Alumnat en cessament disciplinari',
                $knownSections['expulsions'] ?? [],
                'indiqueu l\'alumnat i el motiu.'
            ),
            $this->sectionLine(
                'Alumnat que no ha realitzat la FE (No firma document) / Renúncia (firma document)',
                $knownSections['renuncies'] ?? [],
                'indiqueu l\'alumnat corresponent.'
            ),
        ];

        return implode("\n", $lines);
    }

    /**
     * Elimina d'actes ja creades la instrucció antiga sobre notes reals de 2n.
     *
     * @param OrdenReunion $order
     * @return void
     */
    private function removeDeprecatedSecondYearInstruction(OrdenReunion $order): void
    {
        $summary = (string) $order->resumen;
        if (!str_contains($summary, self::DEPRECATED_SECOND_YEAR_INSTRUCTION)) {
            return;
        }

        $updatedSummary = trim(str_replace(self::DEPRECATED_SECOND_YEAR_INSTRUCTION, '', $summary));
        $order->resumen = $updatedSummary;
        $order->save();
    }

    /**
     * Elimina d'actes ja creades la instrucció antiga sobre anul·lació en secretaria.
     *
     * @param OrdenReunion $order
     * @return void
     */
    private function removeDeprecatedSecretaryInstruction(OrdenReunion $order): void
    {
        $summary = (string) $order->resumen;
        if (!str_contains($summary, self::DEPRECATED_SECRETARY_INSTRUCTION)) {
            return;
        }

        $updatedSummary = trim(str_replace(self::DEPRECATED_SECRETARY_INSTRUCTION, '', $summary));
        $order->resumen = $updatedSummary;
        $order->save();
    }

    /**
     * Construeix els apartats amb les dades FE/FCT del tutor de l'acta.
     *
     * @param string $tutorDni
     * @return array<string, array<int, string>>
     */
    public function knownSectionsForTutor(string $tutorDni): array
    {
        $fcts = $this->lfpFctsForTutor($tutorDni)
            ->filter(static fn (AlumnoFct $fct): bool => (int) ($fct->calProyecto ?? 0) <= 0)
            ->sortBy(static fn (AlumnoFct $fct): string => (string) ($fct->Alumno?->nameFull ?? $fct->Nombre));

        return $this->knownSectionsForFcts($fcts);
    }

    /**
     * Construeix el resum inicial amb les dades FE/FCT del tutor de l'acta.
     *
     * @param string $tutorDni
     * @return string
     */
    public function knownSummaryForTutor(string $tutorDni): string
    {
        return $this->summaryWithKnownData($this->knownSectionsForTutor($tutorDni));
    }

    /**
     * Construeix els apartats amb les dades FE/FCT de l'acta.
     *
     * @param Reunion $reunion
     * @return array<string, array<int, string>>
     */
    private function knownSectionsForReunion(Reunion $reunion): array
    {
        $fcts = $this->lfpFctsForReunion($reunion)
            ->filter(static fn (AlumnoFct $fct): bool => (int) ($fct->calProyecto ?? 0) <= 0)
            ->sortBy(static fn (AlumnoFct $fct): string => (string) ($fct->Alumno?->nameFull ?? $fct->Nombre));

        return $this->knownSectionsForFcts($fcts);
    }

    /**
     * Construeix el resum de notes reals introduïdes per a alumnat no apte, amb cessament o amb renúncia.
     *
     * @param string $tutorDni
     * @param int|null $moduleCourse
     * @return string
     */
    public function notesSummaryForTutor(string $tutorDni, ?int $moduleCourse = null): string
    {
        return $this->notesSummaryForFcts($this->targetFctsForTutor($tutorDni), $moduleCourse);
    }

    /**
     * Construeix el resum de notes reals introduïdes per a una acta concreta.
     *
     * @param Reunion $reunion
     * @return string
     */
    private function notesSummaryForReunion(Reunion $reunion): string
    {
        return $this->notesSummaryForFcts(
            $this->targetFctsForReunion($reunion),
            $this->actaModuleCourse($reunion)
        );
    }

    /**
     * Construeix el resum de notes reals a partir de les FCT objectiu.
     *
     * @param Collection<int, AlumnoFct> $targetFcts
     * @param int|null $moduleCourse
     * @return string
     */
    private function notesSummaryForFcts(Collection $targetFcts, ?int $moduleCourse = null): string
    {
        if ($targetFcts->isEmpty()) {
            return '';
        }

        $alumnos = $targetFcts->keyBy('idAlumno');
        $resultadosQuery = AlumnoResultado::query()
            ->with(['Alumno', 'ModuloGrupo.ModuloCiclo.Modulo'])
            ->whereIn('idAlumno', $alumnos->keys()->all())
            ->whereIn('nota', $this->numericRealGrades());

        if ($moduleCourse !== null && $moduleCourse > 0) {
            $resultadosQuery->whereHas('ModuloGrupo.ModuloCiclo', static function ($query) use ($moduleCourse): void {
                $query->where('curso', (string) $moduleCourse);
            });
        }

        $resultados = $resultadosQuery
            ->get()
            ->groupBy('idAlumno');

        if ($resultados->isEmpty()) {
            return '';
        }

        $rows = [];
        foreach ($alumnos as $idAlumno => $fct) {
            $notes = $resultados->get($idAlumno, collect());
            if ($notes->isEmpty()) {
                continue;
            }

            $moduleRows = $notes
                ->map(function (AlumnoResultado $resultado): string {
                    $nota = (int) $resultado->nota;
                    $label = config('auxiliares.notas')[$nota] ?? $nota;
                    $modulo = $resultado->ModuloGrupo?->ModuloCiclo?->Modulo?->vliteral
                        ?: ($resultado->modulo ?: 'Mòdul ' . $resultado->idModuloGrupo);

                    return '<li><strong>' . e((string) $modulo) . '</strong>: ' . e((string) $label) . '</li>';
                })
                ->implode('');

            $alumno = e((string) ($fct->Alumno?->nameFull ?? $fct->Nombre));
            $rows[] = '<li><strong>' . $alumno . '</strong><ul>' . $moduleRows . '</ul></li>';
        }

        if ($rows === []) {
            return '';
        }

        return '<p>Notes reals introduïdes per a l\'alumnat no apte, amb cessament o amb renúncia:</p><ul>'
            . implode('', $rows)
            . '</ul>';
    }

    /**
     * Retorna l'alumnat de `/avalFct` que necessita notes reals de mòduls.
     *
     * @param string $tutorDni
     * @return Collection<int, AlumnoFct>
     */
    public function targetFctsForTutor(string $tutorDni): Collection
    {
        return $this->targetFctsFromLfpFcts($this->lfpFctsForTutor($tutorDni));
    }

    /**
     * Retorna l'alumnat de l'acta que necessita notes reals de mòduls.
     *
     * @param Reunion $reunion
     * @return Collection<int, AlumnoFct>
     */
    private function targetFctsForReunion(Reunion $reunion): Collection
    {
        return $this->targetFctsFromLfpFcts($this->lfpFctsForReunion($reunion));
    }

    /**
     * Filtra les FCT LFP que necessiten notes reals.
     *
     * @param Collection<int, AlumnoFct> $fcts
     * @return Collection<int, AlumnoFct>
     */
    private function targetFctsFromLfpFcts(Collection $fcts): Collection
    {
        return $fcts
            ->filter(static fn (AlumnoFct $fct): bool => (int) ($fct->calProyecto ?? 0) <= 0)
            ->filter(fn (AlumnoFct $fct): bool => in_array($this->effectiveQualification($fct), [0, 3, 5], true))
            ->sortBy(static fn (AlumnoFct $fct): string => (string) ($fct->Alumno?->nameFull ?? $fct->Nombre))
            ->values();
    }

    /**
     * Prepara les dades per a introduir notes reals de mòduls dins de l'acta ordinària.
     *
     * @param Reunion $reunion
     * @return array{fcts: Collection<int, AlumnoFct>, modulesByStudent: Collection<string, Collection<int, Modulo_grupo>>, results: Collection<string, AlumnoResultado>, gradeOptions: array<int, string>}
     */
    public function gradeInputData(Reunion $reunion): array
    {
        $fcts = $this->targetFctsForReunion($reunion);
        if ($fcts->isEmpty()) {
            return [
                'fcts' => collect(),
                'modulesByStudent' => collect(),
                'results' => collect(),
                'gradeOptions' => $this->validGradeOptions(),
            ];
        }

        $actaCourse = $this->actaModuleCourse($reunion);
        foreach ($fcts as $fct) {
            $fct->loadMissing('Alumno.Grupo.Modulos.ModuloCiclo.Modulo');
        }

        $modulesByStudent = collect();
        $moduleIds = collect();
        foreach ($fcts as $fct) {
            $studentGroups = $fct->Alumno?->Grupo ?? collect();
            if ($actaCourse > 0) {
                $studentGroups = $studentGroups->filter(
                    static fn (Grupo $grupo): bool => (int) ($grupo->curso ?? 0) === $actaCourse
                );
            }

            $modules = $studentGroups->isNotEmpty()
                ? $studentGroups
                    ->flatMap(static function (Grupo $grupo) use ($actaCourse): Collection {
                        return $grupo->Modulos->filter(
                            static fn (Modulo_grupo $modulo): bool => $actaCourse <= 0
                                || (int) ($modulo->ModuloCiclo?->curso ?? 0) === $actaCourse
                        );
                    })
                    ->unique('id')
                    ->sortBy(static fn (Modulo_grupo $modulo): string => (string) $modulo->Xmodulo)
                    ->values()
                : collect();

            $modulesByStudent->put((string) $fct->idAlumno, $modules);
            $moduleIds = $moduleIds->merge($modules->pluck('id'));
        }

        $alumnoIds = $fcts->pluck('idAlumno')->map(static fn ($idAlumno): string => (string) $idAlumno)->unique();
        $moduleIds = $moduleIds->map(static fn ($idModulo): int => (int) $idModulo)->unique()->values();
        $results = $moduleIds->isEmpty()
            ? collect()
            : AlumnoResultado::query()
                ->whereIn('idAlumno', $alumnoIds->all())
                ->whereIn('idModuloGrupo', $moduleIds->all())
                ->get()
                ->keyBy(static fn (AlumnoResultado $resultado): string => $resultado->idAlumno . '-' . $resultado->idModuloGrupo);

        return [
            'fcts' => $fcts,
            'modulesByStudent' => $modulesByStudent,
            'results' => $results,
            'gradeOptions' => $this->validGradeOptions(),
        ];
    }

    /**
     * Guarda notes reals de mòduls per a alumnat no apte, amb cessament o amb renúncia de l'acta.
     *
     * @param Reunion $reunion
     * @param array<string, array<string, array{nota?: mixed, observaciones?: mixed}>> $notes
     * @param array<int, string> $excludedStudents
     * @return int
     */
    public function saveModuleGrades(Reunion $reunion, array $notes, array $excludedStudents = []): int
    {
        $data = $this->gradeInputData($reunion);
        $allowedModules = $data['modulesByStudent'];
        $validGrades = self::VALID_REAL_GRADES;
        $excludedStudents = collect($excludedStudents)->map(static fn ($idAlumno): string => (string) $idAlumno)->all();
        $saved = 0;

        foreach ($excludedStudents as $idAlumno) {
            $studentModules = $allowedModules->get($idAlumno, collect())->pluck('id')->map(
                static fn ($idModulo): int => (int) $idModulo
            );
            if ($studentModules->isEmpty()) {
                continue;
            }

            $saved += AlumnoResultado::query()
                ->where('idAlumno', $idAlumno)
                ->whereIn('idModuloGrupo', $studentModules->all())
                ->delete();
        }

        foreach ($notes as $idAlumno => $modules) {
            if (in_array((string) $idAlumno, $excludedStudents, true)) {
                continue;
            }

            $studentModules = $allowedModules->get((string) $idAlumno, collect())->pluck('id')->map(
                static fn ($idModulo): int => (int) $idModulo
            );
            if ($studentModules->isEmpty()) {
                continue;
            }

            foreach ($modules as $idModuloGrupo => $payload) {
                $idModuloGrupo = (int) $idModuloGrupo;
                if (!$studentModules->contains($idModuloGrupo)) {
                    continue;
                }

                $nota = (int) ($payload['nota'] ?? 0);
                $observaciones = trim((string) ($payload['observaciones'] ?? ''));
                if (!in_array($nota, $validGrades, true)) {
                    continue;
                }

                AlumnoResultado::query()->updateOrCreate(
                    [
                        'idAlumno' => (string) $idAlumno,
                        'idModuloGrupo' => $idModuloGrupo,
                    ],
                    [
                        'nota' => $nota,
                        'observaciones' => $observaciones,
                    ]
                );
                $saved++;
            }
        }

        $this->refreshNotesOrder($reunion);

        return $saved;
    }

    /**
     * Retorna l'alumnat no apte, amb cessament o amb renúncia que encara no té totes les notes de mòduls.
     *
     * @param Reunion $reunion
     * @return array<int, array{alumno: string, modulos: array<int, string>}>
     */
    public function missingModuleGrades(Reunion $reunion): array
    {
        if (!$reunion->avaluacioFinal) {
            return [];
        }

        $data = $this->gradeInputData($reunion);
        $faltants = [];
        foreach ($data['fcts'] as $fct) {
            $resultatsAlumne = $data['results']
                ->filter(static fn (AlumnoResultado $resultado): bool => (string) $resultado->idAlumno === (string) $fct->idAlumno)
                ->keyBy('idModuloGrupo');
            $modulosPendents = [];

            foreach ($data['modulesByStudent']->get((string) $fct->idAlumno, collect()) as $modulo) {
                $resultado = $resultatsAlumne->get($modulo->id);
                $nota = (int) ($resultado?->nota ?? -1);
                if (!$resultado || !in_array($nota, self::VALID_REAL_GRADES, true)) {
                    $modulosPendents[] = (string) ($modulo->Xmodulo ?: $modulo->id);
                }
            }

            if ($modulosPendents !== []) {
                $faltants[] = [
                    'alumno' => (string) ($fct->Alumno?->nameFull ?? $fct->Nombre),
                    'modulos' => $modulosPendents,
                ];
            }
        }

        return $faltants;
    }

    /**
     * Classifica les dades FE/FCT conegudes per a completar els apartats.
     *
     * @param iterable<int, AlumnoFct> $fcts
     * @return array<string, array<int, string>>
     */
    private function knownSectionsForFcts(iterable $fcts): array
    {
        $fcts = collect($fcts);
        return [
            'aptes' => $this->formatFctRows(
                $fcts->filter(fn (AlumnoFct $fct): bool => $this->effectiveQualification($fct) === 1),
                true
            ),
            'no_aptes' => $this->formatFctRows(
                $fcts->filter(fn (AlumnoFct $fct): bool => $this->effectiveQualification($fct) === 0),
                true
            ),
            'convalidats' => $this->formatFctRows(
                $fcts->filter(fn (AlumnoFct $fct): bool => $this->effectiveQualification($fct) === 2)
            ),
            'cessaments' => $this->formatFctRows(
                $fcts->filter(fn (AlumnoFct $fct): bool => $this->effectiveQualification($fct) === 3)
            ),
            'expulsions' => $this->formatExpulsionRows(
                $fcts->filter(fn (AlumnoFct $fct): bool => $this->effectiveQualification($fct) === 4)
            ),
            'renuncies' => $this->formatRenunciaRows(
                $fcts->filter(fn (AlumnoFct $fct): bool => $this->effectiveQualification($fct) === 5)
            )
        ];
    }

    /**
     * Retorna el servei que alimenta el panell `/avalFct`.
     *
     * @return AlumnoFctAvalService
     */
    private function avals(): AlumnoFctAvalService
    {
        if ($this->alumnoFctAvalService === null) {
            $this->alumnoFctAvalService = app(AlumnoFctAvalService::class);
        }

        return $this->alumnoFctAvalService;
    }

    /**
     * Retorna només l'alumnat FE/FCT de normativa LFP del tutor.
     *
     * @param string $tutorDni
     * @return Collection<int, AlumnoFct>
     */
    private function lfpFctsForTutor(string $tutorDni): Collection
    {
        return $this->avals()
            ->latestByProfesor($tutorDni)
            ->filter(fn (AlumnoFct $fct): bool => $this->isLfpFct($fct))
            ->values();
    }

    /**
     * Retorna les FCT LFP de l'acta, preferint el grup docent vinculat.
     *
     * @param Reunion $reunion
     * @return Collection<int, AlumnoFct>
     */
    private function lfpFctsForReunion(Reunion $reunion): Collection
    {
        if ($reunion->idGrupo) {
            return $this->lfpFctsForGrupo((string) $reunion->idGrupo);
        }

        return $this->lfpFctsForTutor((string) $reunion->idProfesor);
    }

    /**
     * Retorna només l'alumnat FE/FCT de normativa LFP d'un grup docent.
     *
     * @param string $idGrupo
     * @return Collection<int, AlumnoFct>
     */
    private function lfpFctsForGrupo(string $idGrupo): Collection
    {
        return AlumnoFct::query()
            ->with(['Alumno.Grupo.Ciclo', 'Fct.Colaboracion.Ciclo'])
            ->whereHas('Alumno.Grupo', static function ($query) use ($idGrupo): void {
                $query->where('grupos.codigo', $idGrupo);
            })
            ->get()
            ->filter(fn (AlumnoFct $fct): bool => $this->isLfpFct($fct))
            ->values();
    }

    /**
     * Indica si una FCT correspon a un grup LFP.
     *
     * @param AlumnoFct $fct
     * @return bool
     */
    private function isLfpFct(AlumnoFct $fct): bool
    {
        $fct->loadMissing(['Alumno.Grupo.Ciclo', 'Fct.Colaboracion.Ciclo']);

        return $this->resolveNormativa($fct, $this->resolveGrupoForFct($fct)) === 'LFP';
    }

    /**
     * Resol el grup de l'alumne vinculat al cicle de la FCT o el primer disponible.
     *
     * @param AlumnoFct $fct
     * @return Grupo|null
     */
    private function resolveGrupoForFct(AlumnoFct $fct): ?Grupo
    {
        $grupos = $fct->Alumno?->Grupo;
        if (!$grupos || $grupos->isEmpty()) {
            return null;
        }

        $cicloId = $fct->Fct?->Colaboracion?->idCiclo;
        if ($cicloId) {
            $grupo = $grupos->firstWhere('idCiclo', $cicloId);
            if ($grupo instanceof Grupo) {
                return $grupo;
            }
        }

        $grupo = $grupos->first();

        return $grupo instanceof Grupo ? $grupo : null;
    }

    /**
     * Determina la normativa efectiva de l'alumne per al punt FE de l'acta.
     *
     * @param AlumnoFct $fct
     * @param Grupo|null $grupo
     * @return string
     */
    private function resolveNormativa(AlumnoFct $fct, ?Grupo $grupo): string
    {
        $nombre = (string) ($grupo?->nombre ?? '');
        if ($nombre !== '') {
            if (preg_match('/\((LOE|LFP|LOGSE)\)/i', $nombre, $matches)) {
                return strtoupper($matches[1]);
            }
            if (stripos($nombre, 'LFP') !== false) {
                return 'LFP';
            }
            if (stripos($nombre, 'LOE') !== false) {
                return 'LOE';
            }
        }

        $normativa = $grupo?->Ciclo?->normativa;
        if (is_string($normativa) && trim($normativa) !== '') {
            return strtoupper($normativa);
        }

        $normativa = $fct->Fct?->Colaboracion?->Ciclo?->normativa;
        if (is_string($normativa) && trim($normativa) !== '') {
            return strtoupper($normativa);
        }

        if ($grupo && str_contains((string) $grupo->codigo, 'LFP')) {
            return 'LFP';
        }

        return 'LOE';
    }

    /**
     * Retorna la qualificació que ha d'usar l'acta FE, aplicant la regla de 1r LFP amb 100 hores.
     *
     * @param AlumnoFct $fct
     * @return int|null
     */
    private function effectiveQualification(AlumnoFct $fct): ?int
    {
        $qualification = $fct->calificacion === null ? null : (int) $fct->calificacion;

        if (in_array($qualification, [null, 0], true) && $this->isCompletedFirstCourseFe($fct)) {
            return 1;
        }

        return $qualification;
    }

    /**
     * Indica si una FE LFP de 1r té les 100 hores completades i s'ha de considerar apta.
     *
     * @param AlumnoFct $fct
     * @return bool
     */
    private function isCompletedFirstCourseFe(AlumnoFct $fct): bool
    {
        $grupo = $this->resolveGrupoForFct($fct);

        return $this->resolveNormativa($fct, $grupo) === 'LFP'
            && (int) ($grupo?->curso ?? 0) === 1
            && (int) ($fct->horasTotal ?? $fct->horas ?? 0) >= 100;
    }

    /**
     * Retorna l'etiqueta de qualificació efectiva per al resum de l'acta.
     *
     * @param AlumnoFct $fct
     * @return string
     */
    private function qualificationLabel(AlumnoFct $fct): string
    {
        return match ($this->effectiveQualification($fct)) {
            0 => 'No Apte',
            1 => 'Apte',
            2 => 'Convalidat/Exempt',
            3 => 'Cessament',
            4 => 'Cessament Disciplinari (Expulsat)',
            5 => 'Renúncia / No realitzada',
            default => 'No Avaluat',
        };
    }

    /**
     * Garantix el punt de notes reals si hi ha notes introduïdes.
     *
     * @param Reunion $reunion
     * @param bool $preserveExistingSummary
     * @return OrdenReunion|null
     */
    public function refreshNotesOrder(Reunion $reunion, bool $preserveExistingSummary = false): ?OrdenReunion
    {
        $existing = OrdenReunion::query()
            ->forReunion($reunion->id)
            ->whereIn('descripcion', [
                self::NOTES_ORDER_DESCRIPTION,
                self::LEGACY_NOTES_ORDER_DESCRIPTION,
                self::LEGACY_NOTES_ORDER_DESCRIPTION_WITH_RENUNCIA,
            ])
            ->first();

        if ($preserveExistingSummary && $existing) {
            if ($existing->descripcion !== self::NOTES_ORDER_DESCRIPTION) {
                $existing->descripcion = self::NOTES_ORDER_DESCRIPTION;
                $existing->save();
            }

            return $existing;
        }

        $summary = $this->notesSummaryForReunion($reunion);
        if ($summary === '') {
            if ($existing) {
                $existing->delete();
            }

            return null;
        }

        if ($existing) {
            if ($existing->descripcion !== self::NOTES_ORDER_DESCRIPTION || $existing->resumen !== $summary) {
                $existing->descripcion = self::NOTES_ORDER_DESCRIPTION;
                $existing->resumen = $summary;
                $existing->save();
            }

            return $existing;
        }

        $nextOrder = ((int) OrdenReunion::query()
            ->forReunion($reunion->id)
            ->max('orden')) + 1;

        return OrdenReunion::create([
            'idReunion' => $reunion->id,
            'orden' => $nextOrder,
            'descripcion' => self::NOTES_ORDER_DESCRIPTION,
            'resumen' => $summary,
        ]);
    }

    /**
     * Retorna les notes que es poden introduir per al formulari FE de l'acta.
     *
     * @return array<int, string>
     */
    public function validGradeOptions(): array
    {
        $labels = config('auxiliares.notas');
        $labels[0] = 'No Superat';

        return collect(self::VALID_REAL_GRADES)
            ->mapWithKeys(static fn (int $grade): array => [$grade => (string) ($labels[$grade] ?? $grade)])
            ->all();
    }

    /**
     * Retorna només les notes numèriques que han d'aparéixer en l'acta.
     *
     * @return array<int, int>
     */
    private function numericRealGrades(): array
    {
        return array_values(array_filter(
            self::VALID_REAL_GRADES,
            static fn (int $grade): bool => $grade >= 5 && $grade <= 10
        ));
    }

    /**
     * Retorna el curs del grup que correspon a l'acta per a filtrar mòduls.
     *
     * @param Reunion $reunion
     * @return int|null
     */
    private function actaModuleCourse(Reunion $reunion): ?int
    {
        $course = (int) ($reunion->GrupoClase?->curso ?? 0);

        return $course > 0 ? $course : null;
    }

    /**
     * Dona format segur a files d'alumnat FCT per al resum HTML de l'acta.
     *
     * @param iterable<int, AlumnoFct> $fcts
     * @param bool $includeHours
     * @return array<int, string>
     */
    private function formatFctRows(iterable $fcts, bool $includeHours = false): array
    {
        $rows = [];
        foreach ($fcts as $fct) {
            $name = e((string) ($fct->Alumno?->nameFull ?? $fct->Nombre));
            $qualification = e($this->qualificationLabel($fct));
            $hours = $includeHours ? ' - ' . (int) $fct->horasTotal . ' hores' : '';
            $rows[] = $name . ' - ' . $qualification . $hours;
        }

        return $rows;
    }

    /**
     * Dona format a l'alumnat amb cessament disciplinari per al resum de l'acta.
     *
     * @param iterable<int, AlumnoFct> $fcts
     * @return array<int, string>
     */
    private function formatExpulsionRows(iterable $fcts): array
    {
        $rows = [];
        foreach ($fcts as $fct) {
            $name = e((string) ($fct->Alumno?->nameFull ?? $fct->Nombre));
            $rows[] = $name . ' - Motiu: (Explica el motiu principal)';
        }

        return $rows;
    }

    /**
     * Dona format a l'alumnat pendent d'indicar renúncia/no realització en l'acta.
     *
     * @param iterable<int, AlumnoFct> $fcts
     * @return array<int, string>
     */
    private function formatRenunciaRows(iterable $fcts): array
    {
        $rows = [];
        foreach ($fcts as $fct) {
            $name = e((string) ($fct->Alumno?->nameFull ?? $fct->Nombre));
            $rows[] = $name . ': Indiqueu si No Realitza o Renúncia';
        }

        return $rows;
    }

    /**
     * Dona format a un apartat de la plantilla, completant-lo si hi ha dades conegudes.
     *
     * @param string $title
     * @param array<int, string> $rows
     * @param string $fallback
     * @return string
     */
    private function sectionLine(string $title, array $rows, string $fallback): string
    {
        if ($rows !== []) {
            return '<p><strong>' . e($title) . ':</strong></p><ul><li>' . implode('</li><li>', $rows) . '</li></ul>';
        }

        return '<p><strong>' . e($title) . ':</strong> ' . e($fallback) . '</p>';
    }

}
