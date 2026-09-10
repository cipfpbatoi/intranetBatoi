<?php

declare(strict_types=1);

namespace Intranet\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Intranet\Application\AssumpteParticular\AssumpteParticularException;
use Intranet\Application\AssumpteParticular\AssumpteParticularService;
use Intranet\Entities\AssumpteParticular;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Panell perquè el professorat sol·licite i consulte assumptes particulars.
 */
class AssumpteParticularProfessorPanel extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public string $dni = '';
    public string $curs = '';
    public string $dataGaudi = '';
    public string $motivacioExcepcional = '';
    public string $plaActivitats = '';
    public string $missatge = '';
    public string $error = '';
    public int $perPage = 10;

    /**
     * @var array<string, array{disponible: float, pendent: int}>
     */
    public array $saldos = [];

    /**
     * @var array{data: string, curs: string, tipus: string, torn: string, saldo: float, excepcional: bool}|null
     */
    public ?array $previsualitzacio = null;

    /**
     * Inicialitza el panell amb l'usuari autenticat.
     */
    public function mount(): void
    {
        $user = authUser();
        abort_unless($user !== null && filled($user->dni ?? null), 403);
        Gate::authorize('create', AssumpteParticular::class);

        $this->dni = (string) $user->dni;
        $this->curs = $this->servei()->cursVigent();
        $this->recarregarSaldos();
    }

    /**
     * Invalida la comprovació anterior quan canvia el formulari.
     */
    public function updated(string $propietat): void
    {
        if (in_array($propietat, ['dataGaudi', 'motivacioExcepcional', 'plaActivitats'], true)) {
            $this->previsualitzacio = null;
            $this->error = '';
        }
    }

    /**
     * Comprova la sol·licitud abans de mostrar el botó de confirmació.
     */
    public function previsualitzar(): void
    {
        $this->resetFeedback();
        $this->validate();

        try {
            $this->previsualitzacio = $this->servei()->previsualitzar(
                $this->dni,
                $this->dataGaudi,
                $this->motivacioExcepcional,
                $this->plaActivitats
            );
        } catch (AssumpteParticularException $exception) {
            $this->error = $exception->getMessage();
            $this->addError('dataGaudi', $exception->getMessage());
        }
    }

    /**
     * Revalida i registra la petició confirmada pel professor.
     */
    public function enviar(): void
    {
        $this->resetFeedback();
        $this->validate();

        if ($this->previsualitzacio === null) {
            $this->previsualitzar();
            return;
        }

        try {
            $this->servei()->crear(
                $this->dni,
                $this->dataGaudi,
                $this->motivacioExcepcional,
                $this->plaActivitats
            );
        } catch (AssumpteParticularException $exception) {
            $this->previsualitzacio = null;
            $this->error = $exception->getMessage();
            $this->addError('dataGaudi', $exception->getMessage());
            return;
        }

        $this->reset(['dataGaudi', 'motivacioExcepcional', 'plaActivitats', 'previsualitzacio']);
        $this->missatge = __('assumptes_particulars.creada');
        $this->resetPage();
        $this->recarregarSaldos();
    }

    /**
     * Cancel·la una petició pròpia pendent.
     */
    public function cancelLarPeticio(int $id): void
    {
        $this->resetFeedback();
        $peticio = AssumpteParticular::query()->findOrFail($id);
        Gate::authorize('cancel', $peticio);

        try {
            $this->servei()->cancelLar($id, $this->dni);
            $this->missatge = __('assumptes_particulars.cancel_lada');
            $this->recarregarSaldos();
        } catch (AssumpteParticularException $exception) {
            $this->error = $exception->getMessage();
        }
    }

    /**
     * Renderitza les peticions exclusivament del professor autenticat.
     */
    public function render(): View
    {
        return view('livewire.assumpte-particular-professor-panel', [
            'peticions' => AssumpteParticular::query()
                ->where('idProfesor', $this->dni)
                ->orderByDesc('data_gaudi')
                ->orderByDesc('id')
                ->paginate($this->perPage),
        ]);
    }

    /**
     * @return array<string, array<int, string>>
     */
    protected function rules(): array
    {
        return [
            'dataGaudi' => ['required', 'date'],
            'motivacioExcepcional' => ['nullable', 'string', 'max:2000'],
            'plaActivitats' => ['nullable', 'string', 'max:10000'],
        ];
    }

    /**
     * Actualitza els saldos i les peticions reservades del curs.
     */
    private function recarregarSaldos(): void
    {
        $this->saldos = $this->servei()->resumSaldo($this->dni, $this->curs);
    }

    /**
     * Neteja els missatges de l'operació anterior.
     */
    private function resetFeedback(): void
    {
        $this->resetValidation();
        $this->missatge = '';
        $this->error = '';
    }

    /**
     * Resol el servei en cada petició Livewire.
     */
    private function servei(): AssumpteParticularService
    {
        return app(AssumpteParticularService::class);
    }
}
