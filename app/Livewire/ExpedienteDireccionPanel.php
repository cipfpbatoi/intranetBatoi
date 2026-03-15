<?php

namespace Intranet\Livewire;

use Intranet\Entities\Expediente;
use Intranet\Services\General\AutorizacionStateService;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Pilot Livewire per al panell d'expedients de Direcció.
 *
 * Manté convivència amb el flux legacy de `/direccion/expediente`.
 */
class ExpedienteDireccionPanel extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    /**
     * @var array<int, array<string, mixed>>
     */
    public array $expedientes = [];

    /**
     * @var array<string, string>
     */
    public array $estatOptions = [];

    public string $filterText = '';
    public string $filterEstat = '';
    public string $error = '';
    public string $message = '';
    public ?int $rebutjarId = null;
    public string $motiuRebutjar = '';
    public int $perPage = 10;
    public bool $hasReadyToPrint = false;
    public bool $hasPendingAuthorization = false;
    public int $readyToPrintCount = 0;
    public int $pendingAuthorizationCount = 0;

    /**
     * @var array<string, mixed>|null
     */
    public ?array $selectedExpediente = null;

    /**
     * Inicialitza el component.
     */
    public function mount(): void
    {
        $this->loadFilterOptions();
        $this->reloadExpedientes();
    }

    /**
     * Reacciona al canvi de filtre textual.
     */
    public function updatedFilterText(): void
    {
        $this->resetPage();
        $this->reloadExpedientes();
    }

    /**
     * Reacciona al canvi de filtre d'estat.
     */
    public function updatedFilterEstat(): void
    {
        $this->resetPage();
        $this->reloadExpedientes();
    }

    /**
     * Autoritza un expedient pendent.
     */
    public function acceptar(int $id): void
    {
        $this->resetFeedback();

        $result = $this->stateService()->accept($id);
        if ($result === false) {
            $this->error = 'No s\'ha pogut autoritzar l\'expedient.';
            return;
        }

        $this->message = 'Expedient autoritzat correctament.';
        $this->reloadExpedientes();
    }

    /**
     * Torna un expedient autoritzat a pendent.
     */
    public function desautoritzar(int $id): void
    {
        $this->resetFeedback();

        $result = $this->stateService()->resign($id);
        if ($result === false) {
            $this->error = 'No s\'ha pogut desfer l\'autorització.';
            return;
        }

        $this->message = 'Expedient retornat a pendent.';
        $this->reloadExpedientes();
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
     * Rebutja un expedient pendent.
     */
    public function confirmarRebutjar(): void
    {
        $this->resetFeedback();

        if ($this->rebutjarId === null) {
            $this->error = 'No hi ha expedient seleccionat per rebutjar.';
            return;
        }

        $result = $this->stateService()->refuse($this->rebutjarId, $this->motiuRebutjar);
        if ($result === false) {
            $this->error = 'No s\'ha pogut rebutjar l\'expedient.';
            return;
        }

        $this->message = 'Expedient rebutjat correctament.';
        $this->cancelarRebutjar();
        $this->reloadExpedientes();
    }

    /**
     * Carrega un expedient per mostrar-lo en modal.
     */
    public function mostrar(int $id): void
    {
        $this->selectedExpediente = collect($this->expedientes)
            ->first(fn (array $expediente): bool => (int) $expediente['id'] === $id);

        if ($this->selectedExpediente !== null) {
            $this->dispatch('show-expediente-modal');
        }
    }

    /**
     * Renderitza la vista del component.
     */
    public function render()
    {
        $paginator = $this->buildFilteredQuery()->paginate($this->perPage);
        $this->expedientes = collect($paginator->items())
            ->map(fn (Expediente $expediente): array => $this->mapExpediente($expediente))
            ->all();

        return view('livewire.expediente-direccion-panel', [
            'paginator' => $paginator,
        ]);
    }

    /**
     * Recarrega flags i resum del llistat.
     */
    private function reloadExpedientes(): void
    {
        $all = $this->buildFilteredQuery()->get();

        $this->readyToPrintCount = $all->filter(
            fn (Expediente $expediente): bool => (int) $expediente->estado === 2
        )->count();
        $this->pendingAuthorizationCount = $all->filter(
            fn (Expediente $expediente): bool => (int) $expediente->estado === 1
        )->count();
        $this->hasReadyToPrint = $this->readyToPrintCount > 0;
        $this->hasPendingAuthorization = $this->pendingAuthorizationCount > 0;
    }

    /**
     * Base query compartida amb filtres.
     *
     * @return mixed
     */
    private function buildFilteredQuery()
    {
        $query = Expediente::query()
            ->with(['Alumno', 'Profesor', 'Modulo', 'tipoExpediente'])
            ->where('estado', '>', 0)
            ->orderByDesc('fecha');

        if ($this->filterText !== '') {
            $this->applyTextFilter($query, $this->filterText);
        }

        if ($this->filterEstat !== '') {
            $query->where('estado', (int) $this->filterEstat);
        }

        return $query;
    }

    /**
     * Normalitza un expedient per a la vista.
     *
     * @return array<string, mixed>
     */
    private function mapExpediente(Expediente $expediente): array
    {
        $nomAlum = $expediente->Alumno->FullName ?? (string) $expediente->idAlumno;
        $nomProfe = $expediente->Profesor->FullName ?? (string) $expediente->idProfesor;
        $tipus = $expediente->tipoExpediente->titulo ?? '-';
        $modul = $expediente->Modulo->literal ?? '';

        return [
            'id' => (int) $expediente->id,
            'nomAlum' => (string) $nomAlum,
            'nomProfe' => (string) $nomProfe,
            'tipo' => (string) $tipus,
            'modulo' => (string) $modul,
            'explicacion' => (string) ($expediente->explicacion ?? ''),
            'fecha' => (string) $expediente->fecha,
            'fechatramite' => (string) ($expediente->fechatramite ?? ''),
            'estado' => (int) $expediente->estado,
            'situacion' => (string) $expediente->situacion,
            'hasDocument' => !empty($expediente->idDocumento),
            'canPdf' => (int) $expediente->estado === 2 || (bool) $expediente->esInforme,
            'canShow' => (int) $expediente->estado > 2,
        ];
    }

    /**
     * Carrega opcions de filtre.
     */
    private function loadFilterOptions(): void
    {
        $this->estatOptions = Expediente::query()
            ->where('estado', '>', 0)
            ->orderBy('estado')
            ->get()
            ->mapWithKeys(function (Expediente $expediente) {
                $key = (string) $expediente->estado;
                return [$key => (string) $expediente->situacion];
            })
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
     * Aplica filtre textual sobre alumne, professor, tipus i mòdul.
     *
     * @param mixed $query
     */
    private function applyTextFilter($query, string $search): void
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

                $innerQuery->where('idAlumno', 'like', $like)
                    ->orWhere('idProfesor', 'like', $like)
                    ->orWhereHas('Alumno', function ($alumnoQuery) use ($like): void {
                        $alumnoQuery->where('nia', 'like', $like)
                            ->orWhere('nombre', 'like', $like)
                            ->orWhere('apellido1', 'like', $like)
                            ->orWhere('apellido2', 'like', $like);
                    })
                    ->orWhereHas('Profesor', function ($profesorQuery) use ($like): void {
                        $profesorQuery->where('dni', 'like', $like)
                            ->orWhere('nombre', 'like', $like)
                            ->orWhere('apellido1', 'like', $like)
                            ->orWhere('apellido2', 'like', $like);
                    })
                    ->orWhereHas('tipoExpediente', function ($tipoQuery) use ($like): void {
                        $tipoQuery->where('titulo', 'like', $like);
                    })
                    ->orWhereHas('Modulo', function ($moduloQuery) use ($like): void {
                        $moduloQuery->where('codigo', 'like', $like)
                            ->orWhere('vliteral', 'like', $like)
                            ->orWhere('cliteral', 'like', $like);
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
            'class' => Expediente::class,
        ]);
    }
}
