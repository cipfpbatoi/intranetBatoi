<?php

namespace Intranet\Livewire;

use Illuminate\Support\Carbon;
use Intranet\Application\Horario\HorarioService;
use Intranet\Entities\Actividad;
use Intranet\Entities\Hora;
use Intranet\Services\General\AutorizacionStateService;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Panell Livewire d'activitats de Direcció.
 */
class ActividadDireccionPanel extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    /**
     * @var array<int, array<string, mixed>>
     */
    public array $actividades = [];

    /**
     * @var array<int, string>
     */
    public array $professorOptions = [];

    /**
     * @var array<string, string>
     */
    public array $estatOptions = [];

    /**
     * @var array<string, string>
     */
    public array $departamentOptions = [];

    public string $filterProfessor = '';
    public string $filterEstat = '';
    public string $filterDepartament = '';
    public string $error = '';
    public string $message = '';
    public ?int $rebutjarId = null;
    public string $motiuRebutjar = '';
    public int $perPage = 10;
    public bool $hasAuthorizedToPrint = false;
    public bool $hasPendingAuthorization = false;
    public int $authorizedToPrintCount = 0;
    public int $pendingAuthorizationCount = 0;

    /**
     * @var array<string, mixed>|null
     */
    public ?array $selectedActividad = null;

    /**
     * Inicialitza el component.
     */
    public function mount(): void
    {
        $this->loadFilterOptions();
        $this->reloadActividades();
    }

    /**
     * Reacciona al canvi de filtre de professor.
     */
    public function updatedFilterProfessor(): void
    {
        $this->resetPage();
        $this->reloadActividades();
    }

    /**
     * Reacciona al canvi de filtre d'estat.
     */
    public function updatedFilterEstat(): void
    {
        $this->resetPage();
        $this->reloadActividades();
    }

    /**
     * Reacciona al canvi de filtre de departament.
     */
    public function updatedFilterDepartament(): void
    {
        $this->resetPage();
        $this->reloadActividades();
    }

    /**
     * Autoritza una activitat pendent.
     */
    public function acceptar(int $id): void
    {
        $this->resetFeedback();

        $result = $this->stateService()->accept($id);
        if ($result === false) {
            $this->error = 'No s\'ha pogut autoritzar l\'activitat.';
            return;
        }

        $this->message = 'Activitat autoritzada correctament.';
        $this->reloadActividades();
    }

    /**
     * Torna una activitat registrada a autoritzada.
     */
    public function desautoritzar(int $id): void
    {
        $this->resetFeedback();

        $result = $this->stateService()->resign($id);
        if ($result === false) {
            $this->error = 'No s\'ha pogut desfer l\'autorització.';
            return;
        }

        $this->message = 'Activitat retornada a l\'estat anterior.';
        $this->reloadActividades();
    }

    /**
     * Obri el formulari de rebuig.
     */
    public function obrirRebutjar(int $id): void
    {
        $this->resetFeedback();
        $this->rebutjarId = $id;
        $this->motiuRebutjar = '';
    }

    /**
     * Tanca el formulari de rebuig.
     */
    public function cancelarRebutjar(): void
    {
        $this->rebutjarId = null;
        $this->motiuRebutjar = '';
    }

    /**
     * Rebutja una activitat amb motiu.
     */
    public function confirmarRebutjar(): void
    {
        $this->resetFeedback();

        if ($this->rebutjarId === null) {
            $this->error = 'No hi ha activitat seleccionada per rebutjar.';
            return;
        }

        $result = $this->stateService()->refuse($this->rebutjarId, $this->motiuRebutjar);
        if ($result === false) {
            $this->error = 'No s\'ha pogut rebutjar l\'activitat.';
            return;
        }

        $this->message = 'Activitat rebutjada correctament.';
        $this->cancelarRebutjar();
        $this->reloadActividades();
    }

    /**
     * Marca una activitat com a tramitada en ITACA.
     */
    public function marcarItaca(int $id): void
    {
        $this->resetFeedback();

        $actividad = Actividad::find($id);
        if (!$actividad) {
            $this->error = 'No s\'ha trobat l\'activitat.';
            return;
        }

        $actividad->estado = 5;
        $actividad->save();

        $this->message = 'Activitat marcada com a tramitada en ITACA.';
        $this->reloadActividades();
    }

    /**
     * Carrega una activitat per mostrar-la en modal.
     */
    public function mostrar(int $id): void
    {
        $this->selectedActividad = collect($this->actividades)
            ->first(fn (array $actividad): bool => (int) $actividad['id'] === $id);

        if ($this->selectedActividad !== null) {
            $this->dispatch('show-actividad-modal');
        }
    }

    /**
     * Renderitza la vista del component.
     */
    public function render()
    {
        $paginator = $this->buildFilteredQuery()->paginate($this->perPage);
        $this->actividades = collect($paginator->items())
            ->map(fn (Actividad $actividad): array => $this->mapActividad($actividad))
            ->all();

        return view('livewire.actividad-direccion-panel', [
            'paginator' => $paginator,
        ]);
    }

    /**
     * Recarrega flags i resum del llistat.
     */
    private function reloadActividades(): void
    {
        $all = $this->buildFilteredQuery()->get();

        $this->authorizedToPrintCount = $all->filter(
            fn (Actividad $actividad): bool => (int) $actividad->estado === 2
        )->count();
        $this->pendingAuthorizationCount = $all->filter(
            fn (Actividad $actividad): bool => (int) $actividad->estado === 1
        )->count();
        $this->hasPendingAuthorization = $all->contains(
            fn (Actividad $actividad): bool => (int) $actividad->estado === 1
        );
        $this->hasAuthorizedToPrint = $all->contains(
            fn (Actividad $actividad): bool => (int) $actividad->estado === 2
        );
    }

    /**
     * Base query compartida amb filtres.
     *
     * @return mixed
     */
    private function buildFilteredQuery()
    {
        $query = Actividad::query()
            ->with(['profesores' => function ($profesores): void {
                $profesores->select('dni', 'nombre', 'apellido1', 'apellido2');
            }, 'grupos:codigo,nombre', 'tipoActividad.departament'])
            ->where('extraescolar', 1)
            ->where('estado', '>', 0)
            ->orderByDesc('desde');

        if ($this->filterProfessor !== '') {
            $this->applySearchFilter($query, $this->filterProfessor);
        }

        if ($this->filterEstat !== '') {
            $query->where('estado', (int) $this->filterEstat);
        }

        if ($this->filterDepartament !== '') {
            $query->whereHas('tipoActividad.departament', function ($departamentQuery): void {
                $departamentQuery->where('id', (int) $this->filterDepartament);
            });
        }

        return $query;
    }

    /**
     * Normalitza una activitat per a la vista.
     *
     * @return array<string, mixed>
     */
    private function mapActividad(Actividad $actividad): array
    {
        $desde = $this->parseActividadDate($actividad, 'desde');
        $hasta = $this->parseActividadDate($actividad, 'hasta');
        $profesores = $actividad->profesores
            ->map(function ($profesor) use ($desde, $hasta): array {
                $label = trim(($profesor->fullName ?? '') ?: ($profesor->dni ?? ''));
                $grupsAfectats = $this->scheduledGroupsForProfesor((string) $profesor->dni, $desde, $hasta);

                return [
                    'dni' => (string) $profesor->dni,
                    'nom' => $label,
                    'teHorariDocent' => $grupsAfectats !== [],
                    'grupsAfectats' => $grupsAfectats,
                ];
            })
            ->filter(fn (array $profesor): bool => $profesor['nom'] !== '')
            ->values();
        $grups = $actividad->grupos
            ->map(fn ($grup) => (string) ($grup->nombre ?? $grup->codigo ?? ''))
            ->filter()
            ->values();

        return [
            'id' => (int) $actividad->id,
            'name' => (string) $actividad->name,
            'descripcion' => (string) ($actividad->descripcion ?? ''),
            'objetivos' => (string) ($actividad->objetivos ?? ''),
            'desde' => (string) $actividad->desde,
            'hasta' => (string) $actividad->hasta,
            'estado' => (int) $actividad->estado,
            'situacion' => (string) $actividad->situacion,
            'tipo' => $actividad->complementaria ? 'Complementària' : 'No complementària',
            'tipoActividad' => (string) ($actividad->tipoActividad->vliteral ?? '-'),
            'justificacioRa' => (string) ($actividad->tipoActividad->justificacio ?? ''),
            'departamento' => (string) ($actividad->tipoActividad->departamento ?? 'CENTRE'),
            'coordinador' => $profesores->first()['nom'] ?? 'Sense assignar',
            'profesores' => $profesores->pluck('nom')->all(),
            'participants' => $profesores->all(),
            'grups' => $grups->all(),
            'hasDocument' => !empty($actividad->idDocumento),
            'idDocumento' => $actividad->idDocumento ? (int) $actividad->idDocumento : null,
            'canDesautorize' => (int) $actividad->estado === 3,
            'canMarkItaca' => (int) $actividad->estado === 4,
            'canPdfValue' => (int) $actividad->estado >= 4,
        ];
    }

    /**
     * Carrega opcions de filtre.
     */
    private function loadFilterOptions(): void
    {
        $this->professorOptions = Actividad::query()
            ->with('profesores')
            ->where('extraescolar', 1)
            ->where('estado', '>', 0)
            ->get()
            ->flatMap(function (Actividad $actividad) {
                return $actividad->profesores->map(function ($profesor) {
                    $dni = (string) $profesor->dni;
                    $label = $profesor->fullName ?? $dni;
                    return trim($label . ' (' . $dni . ')');
                });
            })
            ->unique()
            ->values()
            ->toArray();

        $this->estatOptions = Actividad::query()
            ->where('extraescolar', 1)
            ->where('estado', '>', 0)
            ->orderBy('estado')
            ->get()
            ->mapWithKeys(function (Actividad $actividad) {
                $key = (string) $actividad->estado;
                return [$key => (string) $actividad->situacion];
            })
            ->toArray();

        $this->departamentOptions = Actividad::query()
            ->with('tipoActividad.departament')
            ->where('extraescolar', 1)
            ->where('estado', '>', 0)
            ->get()
            ->map(function (Actividad $actividad) {
                $departament = $actividad->tipoActividad->departament ?? null;

                if (!$departament) {
                    return null;
                }

                return [
                    'id' => (string) $departament->id,
                    'label' => (string) $departament->vliteral,
                ];
            })
            ->filter()
            ->unique('id')
            ->sortBy('label')
            ->mapWithKeys(fn (array $option): array => [$option['id'] => $option['label']])
            ->toArray();
    }

    /**
     * Neteja missatges de feedback.
     */
    private function resetFeedback(): void
    {
        $this->error = '';
        $this->message = '';
    }

    /**
     * Aplica filtre textual per professor.
     *
     * @param mixed $query
     */
    private function applySearchFilter($query, string $search): void
    {
        $terms = collect(preg_split('/\s+/', trim($search)) ?: [])
            ->map(function (string $term): string {
                return trim((string) preg_replace('/[^\pL\pN@._-]+/u', '', $term));
            })
            ->filter()
            ->values()
            ->all();

        foreach ($terms as $term) {
            $query->where(function ($innerQuery) use ($term): void {
                $like = '%' . $term . '%';

                $innerQuery->where('name', 'like', $like)
                    ->orWhereHas('profesores', function ($profesoresQuery) use ($like): void {
                        $profesoresQuery->where('dni', 'like', $like)
                            ->orWhere('nombre', 'like', $like)
                            ->orWhere('apellido1', 'like', $like)
                            ->orWhere('apellido2', 'like', $like);
                    });
            });
        }
    }

    /**
     * Retorna el servei de transicions d'autorització.
     */
    private function stateService(): AutorizacionStateService
    {
        return app()->makeWith(AutorizacionStateService::class, [
            'class' => Actividad::class,
        ]);
    }

    /**
     * Retorna el servei d'horaris.
     */
    private function horarios(): HorarioService
    {
        return app(HorarioService::class);
    }

    /**
     * Parseja la data/hora real de l'activitat evitant accessors de presentació.
     */
    private function parseActividadDate(Actividad $actividad, string $field): Carbon
    {
        return Carbon::parse((string) $actividad->getRawOriginal($field));
    }

    /**
     * Retorna els grups lectius del professor solapats amb l'activitat.
     *
     * @return array<int, string>
     */
    private function scheduledGroupsForProfesor(string $dni, Carbon $desde, Carbon $hasta): array
    {
        $sesiones = Hora::horasAfectadas($desde->format('H:i'), $hasta->format('H:i'))
            ->map(fn ($codigo) => (int) $codigo)
            ->values()
            ->all();

        if ($sesiones === []) {
            return [];
        }

        return $this->horarios()
            ->gruposByProfesorDiaAndSesiones($dni, nameDay($desde->toDateString()), $sesiones)
            ->pluck('idGrupo')
            ->filter()
            ->map(fn ($grup) => (string) $grup)
            ->unique()
            ->values()
            ->all();
    }
}
