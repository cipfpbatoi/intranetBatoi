<?php

declare(strict_types=1);

namespace Intranet\Application\Reunion;

use Illuminate\Support\Collection;
use Intranet\Application\AlumnoFct\AlumnoFctAvalService;
use Intranet\Entities\OrdenReunion;
use Intranet\Entities\AlumnoFct;
use Intranet\Entities\AlumnoResultado;
use Intranet\Entities\Modulo_grupo;
use Intranet\Entities\Reunion;

/**
 * Servei per garantir el punt editable de valoració FE en actes d'avaluació LFP.
 */
class ReunionFeValuationService
{
    public const ORDER_DESCRIPTION = 'Valoració de la FE';
    public const NOTES_ORDER_DESCRIPTION = 'Notes reals dels mòduls de l\'alumnat no apte o amb renúncia';
    private const VALID_REAL_GRADES = [0, 5, 6, 7, 8, 9, 10];

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
            return null;
        }

        $existing = OrdenReunion::query()
            ->forReunion($reunion->id)
            ->where('descripcion', self::ORDER_DESCRIPTION)
            ->first();

        if ($existing) {
            $this->refreshNotesOrder($reunion);

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
        $resolvedNormativa = $normativa === null ? 'LFP' : strtoupper((string) $normativa);

        return (int) $reunion->tipo === 7
            && in_array((int) $reunion->numero, [34, 35], true)
            && $resolvedNormativa === 'LFP';
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
     * Plantilla inicial per a una acta concreta, amb dades conegudes per tutor.
     *
     * @param Reunion $reunion
     * @return string
     */
    public function defaultSummaryForReunion(Reunion $reunion): string
    {
        return $this->summaryWithKnownData($this->knownSectionsForTutor((string) $reunion->idProfesor));
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
            '<p><strong>Alumnat en cessament:</strong> indiqueu l\'alumnat i la justificació.</p>',
            '<p><strong>Alumnat en cessament disciplinari:</strong> indiqueu l\'alumnat i el motiu.</p>',
            $this->sectionLine(
                'Alumnat que no ha realitzat les pràctiques / renúncia',
                $knownSections['renuncies'] ?? [],
                'indiqueu l\'alumnat corresponent.'
            ),
            '<p><strong>Grups de 2n:</strong> indiqueu les notes reals dels mòduls de l\'alumnat no apte o que no ha realitzat la FE quan siga necessari.</p>',
            '<p>El tutor o tutora ha d\'anar a secretaria per a anul·lar la convocatòria extraordinària quan corresponga.</p>',
        ];

        return implode("\n", $lines);
    }

    /**
     * Construeix els apartats amb les dades FE/FCT del tutor de l'acta.
     *
     * @param string $tutorDni
     * @return array<string, array<int, string>>
     */
    public function knownSectionsForTutor(string $tutorDni): array
    {
        $fcts = $this->avals()
            ->latestByProfesor($tutorDni)
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
     * Construeix el resum de notes reals introduïdes per a alumnat no apte o amb renúncia.
     *
     * @param string $tutorDni
     * @return string
     */
    public function notesSummaryForTutor(string $tutorDni): string
    {
        $targetFcts = $this->targetFctsForTutor($tutorDni);
        if ($targetFcts->isEmpty()) {
            return '';
        }

        $alumnos = $targetFcts->keyBy('idAlumno');
        $resultados = AlumnoResultado::query()
            ->with(['Alumno', 'ModuloGrupo.ModuloCiclo.Modulo'])
            ->whereIn('idAlumno', $alumnos->keys()->all())
            ->whereIn('nota', $this->numericRealGrades())
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

        return '<p>Notes reals introduïdes per a l\'alumnat no apte o amb renúncia:</p><ul>'
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
        return $this->avals()
            ->latestByProfesor($tutorDni)
            ->filter(static fn (AlumnoFct $fct): bool => in_array((int) $fct->calificacion, [0, 3], true))
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
        $fcts = $this->targetFctsForTutor((string) $reunion->idProfesor);
        if ($fcts->isEmpty()) {
            return [
                'fcts' => collect(),
                'modulesByStudent' => collect(),
                'results' => collect(),
                'gradeOptions' => $this->validGradeOptions(),
            ];
        }

        foreach ($fcts as $fct) {
            $fct->loadMissing('Alumno.Grupo.Modulos.ModuloCiclo.Modulo');
        }

        $modulesByStudent = collect();
        $moduleIds = collect();
        foreach ($fcts as $fct) {
            $modules = $fct->Alumno?->Grupo
                ? $fct->Alumno->Grupo
                    ->flatMap(static fn ($grupo): Collection => $grupo->Modulos)
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
     * Guarda notes reals de mòduls per a alumnat no apte o amb renúncia de l'acta.
     *
     * @param Reunion $reunion
     * @param array<string, array<string, array{nota?: mixed, observaciones?: mixed}>> $notes
     * @return int
     */
    public function saveModuleGrades(Reunion $reunion, array $notes): int
    {
        $data = $this->gradeInputData($reunion);
        $allowedModules = $data['modulesByStudent'];
        $validGrades = self::VALID_REAL_GRADES;
        $saved = 0;

        foreach ($notes as $idAlumno => $modules) {
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
     * Retorna l'alumnat no apte o amb renúncia que encara no té totes les notes de mòduls.
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
                $fcts->where('calificacion', 1),
                true
            ),
            'no_aptes' => $this->formatFctRows(
                $fcts->where('calificacion', 0),
                true
            ),
            'convalidats' => $this->formatFctRows(
                $fcts->where('calificacion', 2)
            ),
            'renuncies' => $this->formatFctRows(
                $fcts->where('calificacion', 3)
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
     * Garantix el punt de notes reals si hi ha notes introduïdes.
     *
     * @param Reunion $reunion
     * @return OrdenReunion|null
     */
    public function refreshNotesOrder(Reunion $reunion): ?OrdenReunion
    {
        $summary = $this->notesSummaryForTutor((string) $reunion->idProfesor);
        $existing = OrdenReunion::query()
            ->forReunion($reunion->id)
            ->where('descripcion', self::NOTES_ORDER_DESCRIPTION)
            ->first();
        if ($summary === '') {
            if ($existing) {
                $existing->delete();
            }

            return null;
        }

        if ($existing) {
            if ($existing->resumen !== $summary) {
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
            $qualification = e((string) $fct->qualificacio);
            $hours = $includeHours ? ' - ' . (int) $fct->horasTotal . ' hores' : '';
            $rows[] = $name . ' - ' . $qualification . $hours;
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
