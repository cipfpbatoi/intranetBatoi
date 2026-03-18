<?php

namespace Intranet\Livewire;

use Illuminate\Support\Facades\Gate;
use Intranet\Entities\Incidencia;
use Intranet\Entities\OrdenTrabajo;
use Intranet\Services\General\AutorizacionStateService;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Panell Livewire d'incidències per a manteniment.
 */
class IncidenciaMantenimientoPanel extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    /**
     * @var array<int, array<string, mixed>>
     */
    public array $incidencies = [];

    /**
     * @var array<string, string>
     */
    public array $estatOptions = [];

    public string $filterText = '';
    public string $filterEstat = '';
    public string $error = '';
    public string $message = '';
    public int $perPage = 12;
    public ?int $rebutjarId = null;
    public string $motiuRebutjar = '';
    public ?int $resoldreId = null;
    public string $motiuResoldre = '';

    /**
     * @var array<string, mixed>|null
     */
    public ?array $selectedIncidencia = null;

    /**
     * Inicialitza el component.
     */
    public function mount(): void
    {
        Gate::authorize('viewAny', Incidencia::class);
        $this->estatOptions = Incidencia::query()->getModel()->getEstadoOptions();
    }

    /**
     * Reacciona als canvis de filtre de text.
     */
    public function updatedFilterText(): void
    {
        $this->resetPage();
    }

    /**
     * Reacciona als canvis de filtre d'estat.
     */
    public function updatedFilterEstat(): void
    {
        $this->resetPage();
    }

    /**
     * Accepta una incidència pendent.
     */
    public function acceptar(int $id): void
    {
        $this->resetFeedback();

        if (!$this->canManageIncidencia($id)) {
            $this->error = 'No pots gestionar esta incidència.';
            return;
        }

        if ($this->stateService()->accept($id) === false) {
            $this->error = 'No s\'ha pogut autoritzar la incidència.';
            return;
        }

        $this->message = 'Incidència autoritzada correctament.';
    }

    /**
     * Torna una incidència a l'estat anterior.
     */
    public function desautoritzar(int $id): void
    {
        $this->resetFeedback();

        if (!$this->canManageIncidencia($id)) {
            $this->error = 'No pots gestionar esta incidència.';
            return;
        }

        if ($this->stateService()->resign($id) === false) {
            $this->error = 'No s\'ha pogut desfer l\'estat de la incidència.';
            return;
        }

        $this->message = 'Incidència retornada a l\'estat anterior.';
    }

    /**
     * Obri el diàleg de rebuig.
     */
    public function obrirRebutjar(int $id): void
    {
        $this->resetFeedback();
        $this->rebutjarId = $id;
        $this->motiuRebutjar = '';
    }

    /**
     * Tanca el diàleg de rebuig.
     */
    public function cancelarRebutjar(): void
    {
        $this->rebutjarId = null;
        $this->motiuRebutjar = '';
    }

    /**
     * Confirma el rebuig d'una incidència.
     */
    public function confirmarRebutjar(): void
    {
        $this->resetFeedback();

        if ($this->rebutjarId === null || !$this->canManageIncidencia($this->rebutjarId)) {
            $this->error = 'No hi ha incidència seleccionada per rebutjar.';
            return;
        }

        if ($this->stateService()->refuse($this->rebutjarId, $this->motiuRebutjar) === false) {
            $this->error = 'No s\'ha pogut rebutjar la incidència.';
            return;
        }

        $this->message = 'Incidència rebutjada correctament.';
        $this->cancelarRebutjar();
    }

    /**
     * Obri el diàleg de resolució.
     */
    public function obrirResoldre(int $id): void
    {
        $this->resetFeedback();
        $this->resoldreId = $id;
        $this->motiuResoldre = '';
    }

    /**
     * Tanca el diàleg de resolució.
     */
    public function cancelarResoldre(): void
    {
        $this->resoldreId = null;
        $this->motiuResoldre = '';
    }

    /**
     * Confirma la resolució d'una incidència.
     */
    public function confirmarResoldre(): void
    {
        $this->resetFeedback();

        if ($this->resoldreId === null || !$this->canManageIncidencia($this->resoldreId)) {
            $this->error = 'No hi ha incidència seleccionada per resoldre.';
            return;
        }

        if ($this->stateService()->resolve($this->resoldreId, $this->motiuResoldre) === false) {
            $this->error = 'No s\'ha pogut resoldre la incidència.';
            return;
        }

        $this->message = 'Incidència resolta correctament.';
        $this->cancelarResoldre();
    }

    /**
     * Associa o crea una ordre de treball.
     */
    public function assignarOrden(int $id): void
    {
        $this->resetFeedback();

        $incidencia = $this->findManageableIncidencia($id);
        if ($incidencia === null) {
            $this->error = 'No pots gestionar esta incidència.';
            return;
        }

        $orden = OrdenTrabajo::where('tipo', $incidencia->tipo)
            ->where('estado', 0)
            ->where('idProfesor', $this->currentProfesorDni())
            ->first();

        if (!$orden) {
            $orden = new OrdenTrabajo();
            $orden->idProfesor = $this->currentProfesorDni();
            $orden->estado = 0;
            $orden->tipo = $incidencia->tipo;
            $orden->descripcion =
                'Ordre oberta el dia ' . Hoy() . ' pel professor '
                . ($this->currentProfesorName() ?? $this->currentProfesorDni())
                . ' relativa a ' . ($incidencia->Tipos->literal ?? 'incidència');
            $orden->save();
        }

        $incidencia->orden = $orden->id;
        $incidencia->save();

        if ((int) $incidencia->estado === 1) {
            $this->stateService()->accept($incidencia->id);
        }

        $this->message = 'Ordre vinculada correctament.';
    }

    /**
     * Lleva l'ordre associada a una incidència.
     */
    public function llevarOrden(int $id): void
    {
        $this->resetFeedback();

        $incidencia = $this->findManageableIncidencia($id);
        if ($incidencia === null) {
            $this->error = 'No pots gestionar esta incidència.';
            return;
        }

        $incidencia->orden = null;
        $incidencia->save();

        $this->message = 'Ordre desvinculada correctament.';
    }

    /**
     * Carrega una incidència en el modal de detall.
     */
    public function mostrar(int $id): void
    {
        $incidencia = $this->buildVisibleQuery()->find($id);
        if (!$incidencia) {
            $this->error = 'No s\'ha trobat la incidència.';
            return;
        }

        $this->selectedIncidencia = $this->mapIncidencia($incidencia);
        $this->dispatch('show-incidencia-modal');
    }

    /**
     * Renderitza el component.
     */
    public function render()
    {
        $paginator = $this->buildFilteredQuery()->paginate($this->perPage);

        $this->incidencies = collect($paginator->items())
            ->map(fn (Incidencia $incidencia): array => $this->mapIncidencia($incidencia))
            ->all();

        return view('livewire.incidencia-mantenimiento-panel', [
            'paginator' => $paginator,
        ]);
    }

    /**
     * Construeix la query base visible en manteniment.
     */
    private function buildVisibleQuery()
    {
        return Incidencia::query()
            ->with(['Tipos', 'Responsables', 'Creador', 'Espacios', 'Materiales'])
            ->where(function ($query): void {
                $query->where('responsable', $this->currentProfesorDni())
                    ->orWhere(function ($pending): void {
                        $pending->where('estado', 1)
                            ->where(function ($unassigned): void {
                                $unassigned->whereNull('responsable')
                                    ->orWhere('responsable', '');
                            });
                    });
            })
            ->orderByDesc('fecha')
            ->orderByDesc('id');
    }

    /**
     * Aplica filtres sobre la query visible.
     */
    private function buildFilteredQuery()
    {
        $query = $this->buildVisibleQuery();

        if ($this->filterEstat !== '') {
            $query->where('estado', (int) $this->filterEstat);
        }

        if (trim($this->filterText) !== '') {
            $text = trim($this->filterText);

            $query->where(function ($inner) use ($text): void {
                $inner->where('descripcion', 'like', '%' . $text . '%')
                    ->orWhere('observaciones', 'like', '%' . $text . '%')
                    ->orWhere('solucion', 'like', '%' . $text . '%')
                    ->orWhereHas('Tipos', function ($tipoQuery) use ($text): void {
                        $tipoQuery->where('nombre', 'like', '%' . $text . '%')
                            ->orWhere('nom', 'like', '%' . $text . '%');
                    })
                    ->orWhereHas('Creador', function ($profesorQuery) use ($text): void {
                        $profesorQuery->where('nombre', 'like', '%' . $text . '%')
                            ->orWhere('apellido1', 'like', '%' . $text . '%')
                            ->orWhere('apellido2', 'like', '%' . $text . '%');
                    })
                    ->orWhereHas('Responsables', function ($profesorQuery) use ($text): void {
                        $profesorQuery->where('nombre', 'like', '%' . $text . '%')
                            ->orWhere('apellido1', 'like', '%' . $text . '%')
                            ->orWhere('apellido2', 'like', '%' . $text . '%');
                    });
            });
        }

        return $query;
    }

    /**
     * Mapeja l'entitat a estructura de vista.
     *
     * @return array<string, mixed>
     */
    private function mapIncidencia(Incidencia $incidencia): array
    {
        $responsable = trim((string) ($incidencia->responsable ?? ''));
        $orden = $incidencia->orden ? (int) $incidencia->orden : null;
        $estado = (int) $incidencia->estado;

        return [
            'id' => (int) $incidencia->id,
            'descripcion' => (string) $incidencia->descripcion,
            'observaciones' => (string) ($incidencia->observaciones ?? ''),
            'solucion' => (string) ($incidencia->solucion ?? ''),
            'fecha' => (string) ($incidencia->fecha ?? ''),
            'fechasolucion' => (string) ($incidencia->fechasolucion ?? ''),
            'estado' => $estado,
            'situacion' => (string) $incidencia->Xestado,
            'espacio' => (string) ($incidencia->Xespacio ?? ''),
            'material' => (string) ($incidencia->Materiales->descripcion ?? ''),
            'tipo' => (string) ($incidencia->Tipos->literal ?? ''),
            'subtipo' => (string) ($incidencia->subTipo ?? ''),
            'creador' => trim((string) (($incidencia->Creador->nombre ?? '') . ' ' . ($incidencia->Creador->apellido1 ?? ''))),
            'responsable' => $responsable !== ''
                ? trim((string) (($incidencia->Responsables->nombre ?? '') . ' ' . ($incidencia->Responsables->apellido1 ?? '')))
                : 'No assignat',
            'orden' => $orden,
            'imagen' => !empty($incidencia->imagen) ? \Storage::url($incidencia->imagen) : '',
            'canAuthorize' => $estado === 1 && $orden === null,
            'canUnauthorize' => ($estado === 2 && $orden === null) || $estado === 3,
            'canRefuse' => $estado === 1 && $orden === null,
            'canResolve' => $estado === 2 && $orden === null,
            'canAssignOrder' => $orden === null && $estado < 3,
            'canRemoveOrder' => $orden !== null && $estado < 3,
        ];
    }

    /**
     * Busca una incidència gestionable dins del subconjunt visible.
     */
    private function findManageableIncidencia(int $id): ?Incidencia
    {
        return $this->buildVisibleQuery()->find($id);
    }

    /**
     * Indica si una incidència forma part del subconjunt gestionable.
     */
    private function canManageIncidencia(int $id): bool
    {
        return $this->findManageableIncidencia($id) !== null;
    }

    /**
     * Resol el servei de transicions per a incidències.
     */
    private function stateService(): AutorizacionStateService
    {
        return app()->makeWith(AutorizacionStateService::class, [
            'class' => Incidencia::class,
        ]);
    }

    /**
     * Reinicia missatges de feedback.
     */
    private function resetFeedback(): void
    {
        $this->error = '';
        $this->message = '';
    }

    /**
     * Retorna el DNI del professor autenticat.
     */
    private function currentProfesorDni(): string
    {
        $user = AuthUser();
        abort_unless(is_object($user) && isset($user->dni) && (string) $user->dni !== '', 403);

        return (string) $user->dni;
    }

    /**
     * Retorna el nom complet del professor autenticat.
     */
    private function currentProfesorName(): ?string
    {
        $user = AuthUser();
        if (!is_object($user)) {
            return null;
        }

        return $user->FullName ?? $user->fullName ?? null;
    }
}
