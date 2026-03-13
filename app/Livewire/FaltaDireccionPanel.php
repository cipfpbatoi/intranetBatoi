<?php

namespace Intranet\Livewire;

use Intranet\Application\Falta\FaltaService;
use Intranet\Entities\Falta;
use Intranet\Services\General\AutorizacionStateService;
use Livewire\Component;

/**
 * Pilot Livewire per al panell de faltes de Direcció.
 *
 * Manté convivència amb el flux legacy de `/direccion/falta`.
 */
class FaltaDireccionPanel extends Component
{
    /**
     * @var array<int, array<string, mixed>>
     */
    public array $faltes = [];
    /**
     * @var array<string, string>
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

    private ?FaltaService $faltaService = null;

    /**
     * Inicialitza la pantalla i carrega dades.
     */
    public function mount(): void
    {
        $this->loadFilterOptions();
        $this->reloadFaltes();
    }

    /**
     * Reacciona als canvis de filtre.
     */
    public function updatedFilterProfessor(): void
    {
        $this->reloadFaltes();
    }

    /**
     * Reacciona als canvis de filtre.
     */
    public function updatedFilterEstat(): void
    {
        $this->reloadFaltes();
    }

    /**
     * Resol/accepta una falta pendent.
     */
    public function acceptar(int $id): void
    {
        $this->resetFeedback();

        $result = $this->stateService()->resolve($id, null);
        if ($result === false) {
            $this->error = 'No s\\\'ha pogut acceptar la falta.';
            return;
        }

        $this->message = 'Falta acceptada correctament.';
        $this->reloadFaltes();
    }

    /**
     * Mostra el formulari de motiu per a rebutjar.
     */
    public function obrirRebutjar(int $id): void
    {
        $this->resetFeedback();
        $this->rebutjarId = $id;
        $this->motiuRebutjar = '';
    }

    /**
     * Cancela el diàleg de rebuig.
     */
    public function cancelarRebutjar(): void
    {
        $this->rebutjarId = null;
        $this->motiuRebutjar = '';
    }

    /**
     * Rebutja una falta pendent amb motiu.
     */
    public function confirmarRebutjar(): void
    {
        $this->resetFeedback();

        if ($this->rebutjarId === null) {
            $this->error = 'No hi ha falta seleccionada per rebutjar.';
            return;
        }

        $result = $this->stateService()->refuse($this->rebutjarId, $this->motiuRebutjar);
        if ($result === false) {
            $this->error = 'No s\\\'ha pogut rebutjar la falta.';
            return;
        }

        $this->message = 'Falta rebutjada correctament.';
        $this->cancelarRebutjar();
        $this->reloadFaltes();
    }

    /**
     * Marca una baixa com a alta.
     */
    public function alta(int $id): void
    {
        $this->resetFeedback();

        $this->faltas()->alta($id);
        $this->message = 'S\\\'ha tramitat l\\\'alta correctament.';
        $this->reloadFaltes();
    }

    /**
     * Esborra una falta només si encara no està autoritzada.
     */
    public function esborrar(int $id): void
    {
        $this->resetFeedback();

        $falta = Falta::find($id);
        if (!$falta) {
            $this->error = 'No s\\\'ha trobat la falta.';
            return;
        }

        if (!in_array((int) $falta->estado, [1, 2], true)) {
            $this->error = 'Només es poden esborrar faltes sense autoritzar.';
            return;
        }

        $falta->delete();
        $this->message = 'Falta esborrada correctament.';
        $this->reloadFaltes();
    }

    /**
     * Renderitza la vista del component.
     */
    public function render()
    {
        return view('livewire.falta-direccion-panel');
    }

    /**
     * Recarrega el llistat.
     */
    private function reloadFaltes(): void
    {
        $query = Falta::query()
            ->with('Profesor')
            ->orderByDesc('desde');

        if ($this->filterProfessor !== '') {
            $this->applyProfessorFilter($query, $this->filterProfessor);
        }

        if ($this->filterEstat !== '') {
            $query->where('estado', (int) $this->filterEstat);
        }

        $this->faltes = $query->get()->map(function (Falta $falta) {
            return [
                'id' => (int) $falta->id,
                'idProfesor' => (string) $falta->idProfesor,
                'professor' => $falta->Profesor->fullName ?? (string) $falta->idProfesor,
                'desde' => (string) $falta->desde,
                'hasta' => (string) $falta->hasta,
                'motivo' => (string) $falta->motivo,
                'estado' => (int) $falta->estado,
                'situacion' => (string) $falta->situacion,
                'hasDocument' => !empty($falta->fichero),
            ];
        })->all();
    }

    /**
     * Carrega opcions de filtre (professor i estat) des de dades reals.
     */
    private function loadFilterOptions(): void
    {
        $this->professorOptions = Falta::query()
            ->with('Profesor')
            ->orderBy('idProfesor')
            ->get()
            ->map(function (Falta $falta) {
                $dni = (string) $falta->idProfesor;
                $label = $falta->Profesor->fullName ?? $dni;
                return trim($label . ' (' . $dni . ')');
            })
            ->unique()
            ->values()
            ->toArray();

        $this->estatOptions = Falta::query()
            ->orderBy('estado')
            ->get()
            ->mapWithKeys(function (Falta $falta) {
                $key = (string) $falta->estado;
                return [$key => (string) $falta->situacion];
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
     * Aplica filtre textual per professor sobre DNI, nom i cognoms.
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
                    ->orWhereHas('profesor', function ($profesorQuery) use ($like): void {
                        $profesorQuery->where('dni', 'like', $like)
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
            'class' => Falta::class,
        ]);
    }

    /**
     * Retorna el servei de faltes.
     */
    private function faltas(): FaltaService
    {
        if ($this->faltaService === null) {
            $this->faltaService = app(FaltaService::class);
        }

        return $this->faltaService;
    }
}
