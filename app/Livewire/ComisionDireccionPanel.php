<?php

namespace Intranet\Livewire;

use Intranet\Entities\Comision;
use Intranet\Services\General\AutorizacionStateService;
use Livewire\Component;

/**
 * Pilot Livewire per al panell de comissions de Direcció.
 *
 * Manté convivència amb el flux legacy de `/direccion/comision`.
 */
class ComisionDireccionPanel extends Component
{
    /**
     * @var array<int, array<string, mixed>>
     */
    public array $comisiones = [];

    /**
     * @var array<int, string>
     */
    public array $professorOptions = [];

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
    /**
     * @var array<string, mixed>|null
     */
    public ?array $selectedComision = null;

    /**
     * Inicialitza el component.
     */
    public function mount(): void
    {
        $this->loadFilterOptions();
        $this->reloadComisiones();
    }

    /**
     * Reacciona al canvi de filtre de professor.
     */
    public function updatedFilterProfessor(): void
    {
        $this->reloadComisiones();
    }

    /**
     * Reacciona al canvi de filtre d'estat.
     */
    public function updatedFilterEstat(): void
    {
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
     * Renderitza la vista del component.
     */
    public function render()
    {
        return view('livewire.comision-direccion-panel');
    }

    /**
     * Recarrega el llistat de comissions.
     */
    private function reloadComisiones(): void
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

        $this->comisiones = $query->get()->map(function (Comision $comision) {
            return [
                'id' => (int) $comision->id,
                'idProfesor' => (string) $comision->idProfesor,
                'professor' => $comision->Profesor->fullName ?? (string) $comision->idProfesor,
                'servicio' => (string) $comision->servicio,
                'desde' => (string) $comision->desde,
                'hasta' => (string) $comision->hasta,
                'total' => (float) $comision->total,
                'medio' => (string) $comision->tipoVehiculo,
                'kilometraje' => (int) $comision->kilometraje,
                'marca' => (string) ($comision->marca ?? ''),
                'matricula' => (string) ($comision->matricula ?? ''),
                'itinerario' => (string) ($comision->itinerario ?? ''),
                'estado' => (int) $comision->estado,
                'situacion' => (string) $comision->situacion,
            ];
        })->all();
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
     * Retorna el servei de transicions d'autorització.
     */
    private function stateService(): AutorizacionStateService
    {
        return app()->makeWith(AutorizacionStateService::class, [
            'class' => Comision::class,
        ]);
    }
}
