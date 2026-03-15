<?php

namespace Intranet\Livewire;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Intranet\Application\Comision\ComisionService;
use Intranet\Entities\Comision;
use Intranet\Presentation\Crud\ComisionCrudSchema;
use Intranet\Services\General\AutorizacionStateService;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Panell Livewire de comissions de Direcció.
 */
class ComisionDireccionPanel extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    /**
     * @var array<int, array<string, mixed>>
     */
    public array $comisiones = [];

    /**
     * @var array<int, string>
     */
    public array $professorOptions = [];
    /**
     * @var array<int, array<string, mixed>>
     */
    public array $pendingPayments = [];
    public bool $hasAuthorizedToPrint = false;
    public int $authorizedToPrintCount = 0;
    public bool $hasPendingAuthorization = false;
    public int $pendingAuthorizationCount = 0;

    /**
     * @var array<string, string>
     */
    public array $estatOptions = [];

    public string $filterProfessor = '';
    public string $filterEstat = '';
    public string $error = '';
    public string $message = '';
    public ?int $rebutjarId = null;
    public string $motiuRebutjar = '';
    public int $perPage = 10;
    public bool $paymentsExpanded = false;
    public ?int $editComisionId = null;
    public string $editIdProfesor = '';
    public string $editProfessorName = '';
    public string $editDesde = '';
    public string $editHasta = '';
    public bool $editFct = false;
    public string $editServicio = '';
    public string $editAlojamiento = '0';
    public string $editComida = '0';
    public string $editGastos = '0';
    public string $editKilometraje = '0';
    public string $editMedio = '0';
    public string $editMarca = '';
    public string $editMatricula = '';
    public string $editItinerario = '';
    /**
     * @var array<int, string>
     */
    public array $selectedPayments = [];
    /**
     * @var array<string, mixed>|null
     */
    public ?array $selectedComision = null;
    /**
     * @var array<int, string>
     */
    public array $medioOptions = [];

    private ?ComisionService $comisionService = null;

    /**
     * Inicialitza el component.
     */
    public function mount(): void
    {
        $this->loadFilterOptions();
        $this->medioOptions = config('auxiliares.tipoVehiculo');
        $this->reloadComisiones();
    }

    /**
     * Reacciona al canvi de filtre de professor.
     */
    public function updatedFilterProfessor(): void
    {
        $this->resetPage();
        $this->reloadComisiones();
    }

    /**
     * Reacciona al canvi de filtre d'estat.
     */
    public function updatedFilterEstat(): void
    {
        $this->resetPage();
        $this->reloadComisiones();
    }

    /**
     * Autoritza una comissió pendent.
     */
    public function acceptar(int $id): void
    {
        $this->resetFeedback();

        $result = $this->stateService()->accept($id);
        if ($result === false) {
            $this->error = 'No s\'ha pogut autoritzar la comissió.';
            return;
        }

        $this->message = 'Comissió autoritzada correctament.';
        $this->reloadComisiones();
    }

    /**
     * Torna una comissió autoritzada a pendent.
     */
    public function desautoritzar(int $id): void
    {
        $this->resetFeedback();

        $result = $this->stateService()->resign($id);
        if ($result === false) {
            $this->error = 'No s\'ha pogut desfer l\'autorització.';
            return;
        }

        $this->message = 'Comissió retornada a pendent.';
        $this->reloadComisiones();
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
     * Confirma el rebuig d'una comissió pendent.
     */
    public function confirmarRebutjar(): void
    {
        $this->resetFeedback();

        if ($this->rebutjarId === null) {
            $this->error = 'No hi ha comissió seleccionada per rebutjar.';
            return;
        }

        $result = $this->stateService()->refuse($this->rebutjarId, $this->motiuRebutjar);
        if ($result === false) {
            $this->error = 'No s\'ha pogut rebutjar la comissió.';
            return;
        }

        $this->message = 'Comissió rebutjada correctament.';
        $this->cancelarRebutjar();
        $this->reloadComisiones();
    }

    /**
     * Carrega una comissió per mostrar-la en modal.
     */
    public function mostrar(int $id): void
    {
        $this->selectedComision = collect($this->comisiones)
            ->first(fn (array $comision): bool => (int) $comision['id'] === $id);

        if ($this->selectedComision !== null) {
            $this->dispatch('show-comision-modal');
        }
    }

    /**
     * Carrega la comissió en el modal d'edició propi del component.
     */
    public function editar(int $id): void
    {
        $this->resetFeedback();

        $comision = Comision::with('Profesor')->find($id);
        if (!$comision) {
            $this->error = 'No s\'ha trobat la comissió.';
            return;
        }

        if ((int) $comision->estado >= 3) {
            $this->error = 'No es poden editar comissions registrades o posteriors.';
            return;
        }

        $this->editComisionId = (int) $comision->id;
        $this->editIdProfesor = (string) $comision->idProfesor;
        $this->editProfessorName = $comision->Profesor->fullName ?? (string) $comision->idProfesor;
        $this->editDesde = $this->formatForInput($comision->getRawOriginal('desde'));
        $this->editHasta = $this->formatForInput($comision->getRawOriginal('hasta'));
        $this->editFct = (int) $comision->fct === 1;
        $this->editServicio = (string) $comision->servicio;
        $this->editAlojamiento = (string) $comision->alojamiento;
        $this->editComida = (string) $comision->comida;
        $this->editGastos = (string) $comision->gastos;
        $this->editKilometraje = (string) $comision->kilometraje;
        $this->editMedio = (string) $comision->medio;
        $this->editMarca = (string) ($comision->marca ?? '');
        $this->editMatricula = (string) ($comision->matricula ?? '');
        $this->editItinerario = (string) ($comision->itinerario ?? '');
        $this->dispatch('show-edit-comision-modal');
    }

    /**
     * Tanca i neteja el modal d'edició.
     */
    public function cancelarEditar(): void
    {
        $this->resetEditForm();
        $this->dispatch('hide-edit-comision-modal');
    }

    /**
     * Manté coherència de camps derivats quan canvia FCT.
     */
    public function updatedEditFct(bool $value): void
    {
        if ($value) {
            $this->editServicio = 'Visita empreses FCT:';
            $this->editAlojamiento = '0';
            $this->editComida = '0';
        }
    }

    /**
     * Desactiva l'itinerari si no hi ha kilometratge.
     */
    public function updatedEditKilometraje($value): void
    {
        if ((float) str_replace(',', '.', (string) $value) <= 0) {
            $this->editItinerario = '';
        }
    }

    /**
     * Guarda l'edició de la comissió directament des del component.
     */
    public function guardarEdicio(): void
    {
        $this->resetFeedback();

        if ($this->editComisionId === null) {
            $this->error = 'No hi ha cap comissió carregada per editar.';
            return;
        }

        $comision = Comision::find($this->editComisionId);
        if (!$comision) {
            $this->error = 'No s\'ha trobat la comissió.';
            return;
        }

        $this->validateEditForm();

        $request = new Request([
            'idProfesor' => $this->editIdProfesor,
            'desde' => $this->editDesde,
            'hasta' => $this->editHasta,
            'fct' => $this->editFct ? '1' : null,
            'servicio' => $this->editServicio,
            'alojamiento' => $this->editFct ? '0' : $this->editAlojamiento,
            'comida' => $this->editFct ? '0' : $this->editComida,
            'gastos' => $this->editGastos,
            'kilometraje' => $this->editKilometraje,
            'medio' => $this->editMedio,
            'marca' => $this->editMarca,
            'matricula' => $this->editMatricula,
            'itinerario' => ((float) str_replace(',', '.', $this->editKilometraje) > 0) ? $this->editItinerario : '',
        ]);

        $comision->fillAll($request);

        $this->message = 'Comissió actualitzada correctament.';
        $this->resetEditForm();
        $this->reloadComisiones();
        $this->dispatch('hide-edit-comision-modal');
    }

    /**
     * Esborra una comissió mentre no conste com a cobrada.
     */
    public function esborrar(int $id): void
    {
        $this->resetFeedback();

        $comision = Comision::find($id);
        if (!$comision) {
            $this->error = 'No s\'ha trobat la comissió.';
            return;
        }

        if ((int) $comision->estado >= 5) {
            $this->error = 'No es poden esborrar comissions cobrades.';
            return;
        }

        $comision->delete();
        $this->message = 'Comissió esborrada correctament.';
        $this->reloadComisiones();
    }

    /**
     * Marca en bloc professors com a pendents de cobrament i obri l'informe.
     */
    public function imprimirPagamentsSeleccionats()
    {
        $this->resetFeedback();

        $dnis = array_values(array_unique(array_filter($this->selectedPayments)));
        if ($dnis === []) {
            $this->error = 'Selecciona almenys un professor per imprimir pagaments.';
            return null;
        }

        $this->comisions()->prePayByProfesores($dnis);
        $this->dispatch('open-report-and-reload', url: route('comision.direccion.paid'), delay: 1200);
        $this->message = 'S\'ha obert l\'informe de pagaments. El panell es recarregarà automàticament.';

        return null;
    }

    /**
     * Autoritza en bloc totes les comissions pendents visibles al flux de Direcció.
     */
    public function autoritzarPendents(): void
    {
        $this->resetFeedback();

        $updated = $this->comisions()->authorizeAllPending();
        if ($updated === 0) {
            $this->error = 'No hi ha comissions pendents per autoritzar.';
            return;
        }

        $this->message = $updated === 1
            ? 'S\'ha autoritzat 1 comissió pendent.'
            : 'S\'han autoritzat ' . $updated . ' comissions pendents.';

        $this->reloadComisiones();
    }

    /**
     * Obri l'informe d'autoritzades i recarrega el panell quan finalitze.
     *
     * @return null
     */
    public function imprimirAutoritzades()
    {
        $this->resetFeedback();

        if (!$this->hasAuthorizedToPrint) {
            $this->error = 'No hi ha comissions autoritzades per imprimir.';
            return null;
        }

        $this->dispatch(
            'open-report-and-reload',
            url: route('comision.direccion.pdf'),
            delay: 1200
        );
        $this->message = 'S\'ha obert l\'informe de comissions autoritzades. El panell es recarregarà automàticament.';

        return null;
    }

    /**
     * Alterna la visibilitat del bloc de pagaments pendents.
     */
    public function togglePendingPayments(): void
    {
        $this->paymentsExpanded = !$this->paymentsExpanded;
    }

    /**
     * Renderitza la vista del component.
     */
    public function render()
    {
        $paginator = $this->buildFilteredQuery()->paginate($this->perPage);
        $this->comisiones = collect($paginator->items())
            ->map(fn (Comision $comision): array => $this->mapComision($comision))
            ->all();

        return view('livewire.comision-direccion-panel', [
            'paginator' => $paginator,
        ]);
    }

    /**
     * Recarrega el llistat de comissions.
     */
    private function reloadComisiones(): void
    {
        $query = $this->buildFilteredQuery();
        $all = $query->get();

        $this->loadPendingPayments();
        $this->authorizedToPrintCount = $all->filter(
            fn (Comision $comision): bool => (int) $comision->estado === 2
        )->count();
        $this->hasAuthorizedToPrint = $all->contains(
            fn (Comision $comision): bool => (int) $comision->estado === 2
        );
        $this->pendingAuthorizationCount = $all->filter(
            fn (Comision $comision): bool => (int) $comision->estado === 1
        )->count();
        $this->hasPendingAuthorization = $all->contains(
            fn (Comision $comision): bool => (int) $comision->estado === 1
        );
        $this->selectedPayments = array_values(array_intersect(
            $this->selectedPayments,
            array_column($this->pendingPayments, 'dni')
        ));
    }

    /**
     * Base query compartida amb filtres aplicats.
     *
     * @return mixed
     */
    private function buildFilteredQuery()
    {
        $query = Comision::query()
            ->with('Profesor')
            ->where('estado', '>', 0)
            ->orderByDesc('desde');

        if ($this->filterProfessor !== '') {
            $this->applyProfessorFilter($query, $this->filterProfessor);
        }

        if ($this->filterEstat !== '') {
            $query->where('estado', (int) $this->filterEstat);
        }

        return $query;
    }

    /**
     * Normalitza una comissió per a la vista.
     *
     * @return array<string, mixed>
     */
    private function mapComision(Comision $comision): array
    {
        return [
            'id' => (int) $comision->id,
            'idProfesor' => (string) $comision->idProfesor,
            'professor' => $comision->Profesor->fullName ?? (string) $comision->idProfesor,
            'servicio' => (string) $comision->servicio,
            'desde' => (string) $comision->desde,
            'hasta' => (string) $comision->hasta,
            'fct' => (int) $comision->fct,
            'alojamiento' => (string) $comision->alojamiento,
            'comida' => (string) $comision->comida,
            'gastos' => (string) $comision->gastos,
            'total' => (float) $comision->total,
            'medioCodigo' => (int) $comision->medio,
            'medio' => (string) $comision->tipoVehiculo,
            'kilometraje' => (int) $comision->kilometraje,
            'marca' => (string) ($comision->marca ?? ''),
            'matricula' => (string) ($comision->matricula ?? ''),
            'itinerario' => (string) ($comision->itinerario ?? ''),
            'estado' => (int) $comision->estado,
            'situacion' => (string) $comision->situacion,
            'canEdit' => (int) $comision->estado < 3,
            'canDelete' => (int) $comision->estado < 5,
            'hasDocument' => !empty($comision->idDocumento),
            'idDocumento' => $comision->idDocumento ? (int) $comision->idDocumento : null,
        ];
    }

    /**
     * Carrega opcions de filtre.
     */
    private function loadFilterOptions(): void
    {
        $this->professorOptions = Comision::query()
            ->with('Profesor')
            ->where('estado', '>', 0)
            ->orderBy('idProfesor')
            ->get()
            ->map(function (Comision $comision) {
                $dni = (string) $comision->idProfesor;
                $label = $comision->Profesor->fullName ?? $dni;
                return trim($label . ' (' . $dni . ')');
            })
            ->unique()
            ->values()
            ->toArray();

        $this->estatOptions = Comision::query()
            ->where('estado', '>', 0)
            ->orderBy('estado')
            ->get()
            ->mapWithKeys(function (Comision $comision) {
                $key = (string) $comision->estado;
                return [$key => (string) $comision->situacion];
            })
            ->toArray();
    }

    /**
     * Aplica filtre textual per professor.
     *
     * @param mixed $query
     */
    private function applyProfessorFilter($query, string $search): void
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

                $innerQuery->where('idProfesor', 'like', $like)
                    ->orWhereHas('Profesor', function ($profesorQuery) use ($like): void {
                        $profesorQuery->where('dni', 'like', $like)
                            ->orWhere('nombre', 'like', $like)
                            ->orWhere('apellido1', 'like', $like)
                            ->orWhere('apellido2', 'like', $like);
                    });
            });
        }
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
     * Valida les dades del formulari d'edició.
     */
    private function validateEditForm(): void
    {
        $rules = ComisionCrudSchema::requestRules(authUser()->dni == config('avisos.director'));
        $validator = Validator::make([
            'servicio' => $this->editServicio,
            'kilometraje' => $this->editKilometraje,
            'desde' => $this->editDesde,
            'hasta' => $this->editHasta,
            'alojamiento' => $this->editFct ? '0' : $this->editAlojamiento,
            'comida' => $this->editFct ? '0' : $this->editComida,
            'gastos' => $this->editGastos,
            'medio' => $this->editMedio,
            'marca' => $this->editMarca,
            'matricula' => $this->editMatricula,
        ], $rules);

        $validated = $validator->validate();

        $this->editKilometraje = (string) $validated['kilometraje'];
        $this->editGastos = (string) $validated['gastos'];
        $this->editAlojamiento = (string) $validated['alojamiento'];
        $this->editComida = (string) $validated['comida'];
    }

    /**
     * Retorna el servei de transicions d'autorització.
     */
    private function stateService(): AutorizacionStateService
    {
        return app()->makeWith(AutorizacionStateService::class, [
            'class' => Comision::class,
        ]);
    }

    /**
     * Recupera professors amb comissions en estat pendent de pagament.
     */
    private function loadPendingPayments(): void
    {
        $this->pendingPayments = Comision::query()
            ->with('Profesor')
            ->where('estado', 4)
            ->orderBy('idProfesor')
            ->get()
            ->groupBy('idProfesor')
            ->map(function ($grupo, string $dni): array {
                /** @var Comision $first */
                $first = $grupo->first();

                return [
                    'dni' => $dni,
                    'professor' => $first->Profesor->fullName ?? $dni,
                    'total' => (float) $grupo->sum('total'),
                    'count' => $grupo->count(),
                ];
            })
            ->values()
            ->all();
    }

    /**
     * Resol el servei d'aplicació de comissions.
     */
    private function comisions(): ComisionService
    {
        if ($this->comisionService === null) {
            $this->comisionService = app(ComisionService::class);
        }

        return $this->comisionService;
    }

    /**
     * Dona format per a inputs `datetime-local`.
     */
    private function formatForInput(?string $value): string
    {
        if (!$value) {
            return '';
        }

        return Carbon::parse($value)->format('Y-m-d\TH:i');
    }

    /**
     * Neteja l'estat intern del formulari d'edició.
     */
    private function resetEditForm(): void
    {
        $this->editComisionId = null;
        $this->editIdProfesor = '';
        $this->editProfessorName = '';
        $this->editDesde = '';
        $this->editHasta = '';
        $this->editFct = false;
        $this->editServicio = '';
        $this->editAlojamiento = '0';
        $this->editComida = '0';
        $this->editGastos = '0';
        $this->editKilometraje = '0';
        $this->editMedio = '0';
        $this->editMarca = '';
        $this->editMatricula = '';
        $this->editItinerario = '';
        $this->resetValidation();
    }
}
