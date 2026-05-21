<?php

namespace Intranet\Livewire;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Intranet\Application\Falta\FaltaService;
use Intranet\Entities\Falta;
use Intranet\Entities\Profesor;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Intranet\Services\General\AutorizacionStateService;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * Panell Livewire de faltes de Direcció.
 */
class FaltaDireccionPanel extends Component
{
    use AuthorizesRequests;
    use WithFileUploads;

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
    public bool $isDireccion = false;
    public ?int $rebutjarId = null;
    public string $motiuRebutjar = '';
    public bool $showFormModal = false;
    public bool $isEditing = false;
    public ?int $formFaltaId = null;
    public string $formIdProfesor = '';
    public string $formProfessorSearch = '';
    public string $formDesde = '';
    public string $formHasta = '';
    public bool $formBaja = false;
    public bool $formDiaCompleto = true;
    public string $formHoraIni = '';
    public string $formHoraFin = '';
    public string $formMotivos = '';
    public string $formObservaciones = '';
    public ?TemporaryUploadedFile $formFichero = null;
    public string $existingFichero = '';

    private ?FaltaService $faltaService = null;

    /**
     * Inicialitza la pantalla i carrega dades.
     */
    public function mount(): void
    {
        $this->isDireccion = AuthUser() ? esRol(AuthUser()->rol, config('roles.rol.direccion')) : false;
        $this->loadFilterOptions();
        $this->resetForm();
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
     * Sincronitza el professor seleccionat des del cercador del modal.
     */
    public function updatedFormProfessorSearch(string $value): void
    {
        $normalized = $this->normalizeProfessorLabel($value);

        foreach ($this->professorOptions as $dni => $label) {
            if ($normalized === $this->normalizeProfessorLabel($label) || trim($value) === $dni) {
                $this->formIdProfesor = $dni;
                return;
            }
        }
    }

    /**
     * Ajusta hores quan canvia el tipus de falta.
     */
    public function updatedFormDiaCompleto(bool $value): void
    {
        if ($value) {
            $this->formHoraIni = '';
            $this->formHoraFin = '';
        }
    }

    /**
     * Ajusta camps derivats quan es marca una baixa llarga.
     */
    public function updatedFormBaja(bool $value): void
    {
        if ($value) {
            $this->formDiaCompleto = true;
            $this->formHoraIni = '';
            $this->formHoraFin = '';
            $this->formHasta = $this->formDesde;
        }
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
     * Obri el modal en mode creació.
     */
    public function crear(): void
    {
        $this->resetFeedback();
        $this->resetForm();
        $this->showFormModal = true;
        $this->dispatch('show-falta-modal');
    }

    /**
     * Obri el modal en mode edició.
     */
    public function editar(int $id): void
    {
        $this->resetFeedback();

        $falta = Falta::find($id);
        if (!$falta) {
            $this->error = 'No s\\\'ha trobat la falta.';
            return;
        }

        $this->isEditing = true;
        $this->formFaltaId = (int) $falta->id;
        $this->formIdProfesor = (string) $falta->idProfesor;
        $this->formProfessorSearch = $this->professorOptions[$this->formIdProfesor] ?? (string) $falta->idProfesor;
        $this->formDesde = (string) $falta->getRawOriginal('desde');
        $this->formHasta = (string) ($falta->getRawOriginal('hasta') ?: $falta->getRawOriginal('desde'));
        $this->formBaja = (int) $falta->baja === 1;
        $this->formDiaCompleto = (int) $falta->dia_completo === 1;
        $this->formHoraIni = (string) $falta->getRawOriginal('hora_ini');
        $this->formHoraFin = (string) $falta->getRawOriginal('hora_fin');
        $this->formMotivos = (string) $falta->motivos;
        $this->formObservaciones = (string) $falta->observaciones;
        $this->existingFichero = (string) ($falta->fichero ?? '');
        $this->showFormModal = true;

        $this->dispatch('show-falta-modal');
    }

    /**
     * Tanca el modal de formulari.
     */
    public function cancelForm(): void
    {
        $this->resetForm();
        $this->dispatch('hide-falta-modal');
    }

    /**
     * Guarda la falta des del propi component Livewire.
     */
    public function guardar(): void
    {
        $this->resetFeedback();
        $this->syncProfessorSearch();
        $this->validate($this->formRules(), [], $this->validationAttributes());

        if ($this->formFaltaId !== null) {
            $falta = Falta::find($this->formFaltaId);
            if (!$falta) {
                $this->error = 'No s\\\'ha trobat la falta.';
                return;
            }

            $this->authorize('update', $falta);
            $this->faltas()->update($this->formFaltaId, $this->buildFormRequest());
            $this->message = 'Falta actualitzada correctament.';
        } else {
            $this->authorize('create', Falta::class);
            $id = $this->faltas()->create($this->buildFormRequest());

            if (!$this->formBaja) {
                $this->faltas()->init($id);
            }

            $this->message = 'Falta creada correctament.';
        }

        $this->resetForm();
        $this->reloadFaltes();
        $this->dispatch('hide-falta-modal');
    }

    /**
     * Esborra una falta només si està no enviada o pendent de tramitació.
     */
    public function esborrar(int $id): void
    {
        $this->resetFeedback();

        $falta = Falta::find($id);
        if (!$falta) {
            $this->error = 'No s\\\'ha trobat la falta.';
            return;
        }

        if (!in_array((int) $falta->estado, [0, 1, 2], true)) {
            $this->error = 'Només es poden esborrar faltes sense autoritzar.';
            return;
        }

        if ((int) $falta->estado === 0 && !$this->isDireccion) {
            $this->error = 'No tens permisos per esborrar faltes no enviades.';
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
        $this->professorOptions = Profesor::query()
            ->Activo()
            ->orderBy('apellido1')
            ->orderBy('apellido2')
            ->orderBy('nombre')
            ->get()
            ->mapWithKeys(function (Profesor $profesor): array {
                $label = trim($profesor->fullName . ' (' . $profesor->dni . ')');
                return [(string) $profesor->dni => $label];
            })
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
     * Reinicialitza el formulari modal.
     */
    private function resetForm(): void
    {
        $this->showFormModal = false;
        $this->isEditing = false;
        $this->formFaltaId = null;
        $this->formIdProfesor = AuthUser()->dni ?? '';
        $this->formProfessorSearch = $this->professorOptions[$this->formIdProfesor] ?? '';
        $this->formDesde = now()->format('Y-m-d');
        $this->formHasta = now()->format('Y-m-d');
        $this->formBaja = false;
        $this->formDiaCompleto = true;
        $this->formHoraIni = '';
        $this->formHoraFin = '';
        $this->formMotivos = '';
        $this->formObservaciones = '';
        $this->formFichero = null;
        $this->existingFichero = '';
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

    /**
     * Regles del formulari de crear/editar.
     *
     * @return array<string, string>
     */
    private function formRules(): array
    {
        return [
            'formIdProfesor' => 'required',
            'formDesde' => 'exclude_if:formBaja,1|required|date',
            'formHasta' => 'exclude_if:formBaja,1|nullable|date',
            'formMotivos' => 'required',
            'formObservaciones' => 'nullable|max:200',
            'formHoraIni' => 'exclude_if:formBaja,1|required_unless:formDiaCompleto,1,true,on',
            'formHoraFin' => 'exclude_if:formBaja,1|required_unless:formDiaCompleto,1,true,on',
            'formFichero' => 'nullable|mimes:pdf,jpg,jpeg,png',
        ];
    }

    /**
     * Etiquetes llegibles per als errors de validació.
     *
     * @return array<string, string>
     */
    private function validationAttributes(): array
    {
        return [
            'formIdProfesor' => 'professor',
            'formDesde' => 'data d\'inici',
            'formHasta' => 'data de fi',
            'formMotivos' => 'motiu',
            'formObservaciones' => 'observacions',
            'formHoraIni' => 'hora d\'inici',
            'formHoraFin' => 'hora de fi',
            'formFichero' => 'fitxer',
        ];
    }

    /**
     * Construeix el request que espera el servei d'aplicació.
     */
    private function buildFormRequest(): Request
    {
        $payload = [
            'idProfesor' => $this->formIdProfesor,
            'desde' => $this->formDesde,
            'hasta' => $this->formHasta !== '' ? $this->formHasta : $this->formDesde,
            'baja' => $this->formBaja ? '1' : '0',
            'dia_completo' => $this->formDiaCompleto ? '1' : '0',
            'hora_ini' => $this->formDiaCompleto ? null : $this->formHoraIni,
            'hora_fin' => $this->formDiaCompleto ? null : $this->formHoraFin,
            'motivos' => $this->formMotivos,
            'observaciones' => $this->formObservaciones,
        ];

        $files = [];
        if ($this->formFichero !== null) {
            $files['fichero'] = $this->formFichero;
        }

        return Request::create('/direccion/falta', 'POST', $payload, [], $files);
    }

    /**
     * Intenta fixar l'identificador de professor a partir del text del cercador.
     */
    private function syncProfessorSearch(): void
    {
        if ($this->formIdProfesor !== '') {
            return;
        }

        $this->updatedFormProfessorSearch($this->formProfessorSearch);
    }

    /**
     * Normalitza una etiqueta de professor per a comparacions de cercador.
     */
    private function normalizeProfessorLabel(string $value): string
    {
        $normalized = preg_replace('/\s+/', ' ', trim($value));
        return mb_strtolower((string) $normalized);
    }
}
