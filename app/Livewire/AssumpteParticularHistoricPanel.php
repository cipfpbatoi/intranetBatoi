<?php

declare(strict_types=1);

namespace Intranet\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Intranet\Application\AssumpteParticular\AssumpteParticularDireccionQueryService;
use Intranet\Application\AssumpteParticular\AssumpteParticularException;
use Intranet\Application\AssumpteParticular\AssumpteParticularService;
use Intranet\Entities\AssumpteParticular;
use Intranet\Entities\Profesor;
use Livewire\Component;

/**
 * Històric filtrable d'autoritzacions d'assumptes particulars de Direcció.
 */
class AssumpteParticularHistoricPanel extends Component
{
    /** @var array<int, array<string, mixed>> */
    public array $historial = [];
    /** @var array<string, string> */
    public array $professors = [];
    public string $filtreProfessor = '';
    public string $filtreData = '';
    public string $filtreTipus = '';
    public string $filtreOrigen = '';
    public string $professorRegularitzacio = '';
    public string $dataRegularitzacio = '';
    public string $tipusRegularitzacio = AssumpteParticular::TIPUS_LECTIU;
    public string $missatge = '';
    public string $error = '';

    /**
     * Comprova l'accés exclusiu de Direcció i prepara els filtres.
     */
    public function mount(): void
    {
        $user = authUser();
        abort_unless(
            $user !== null && esRol($user->rol, config('roles.rol.direccion')),
            403
        );

        $this->professors = Profesor::query()
            ->activo()
            ->orderBy('apellido1')
            ->orderBy('apellido2')
            ->orderBy('nombre')
            ->get()
            ->mapWithKeys(static fn (Profesor $professor): array => [
                (string) $professor->dni => $professor->fullName,
            ])
            ->all();

        $this->recarregar();
    }

    /**
     * Recarrega l'històric aplicant els filtres seleccionats.
     */
    public function recarregar(): void
    {
        $this->validate([
            'filtreProfessor' => ['nullable', 'string', 'exists:profesores,dni'],
            'filtreData' => ['nullable', 'date_format:Y-m-d'],
            'filtreTipus' => ['nullable', 'in:lectiu,no_lectiu'],
            'filtreOrigen' => ['nullable', 'in:sollicitud,regularitzacio'],
        ]);

        $this->historial = app(AssumpteParticularDireccionQueryService::class)->autoritzades(
            app(AssumpteParticularService::class)->cursVigent(),
            filled($this->filtreProfessor) ? $this->filtreProfessor : null,
            filled($this->filtreData) ? $this->filtreData : null,
            filled($this->filtreTipus) ? $this->filtreTipus : null,
            filled($this->filtreOrigen) ? $this->filtreOrigen : null
        );
    }

    /** Recarrega automàticament quan canvia qualsevol filtre. */
    public function updated(string $property): void
    {
        if (str_starts_with($property, 'filtre')) {
            $this->recarregar();
        }
    }

    /** Neteja tots els filtres de l'històric. */
    public function netejarFiltres(): void
    {
        $this->reset(['filtreProfessor', 'filtreData', 'filtreTipus', 'filtreOrigen']);
        $this->resetValidation();
        $this->recarregar();
    }

    /**
     * Registra un dia ja gaudit i autoritzat fora de la intranet.
     */
    public function regularitzar(): void
    {
        Gate::authorize('regularize', AssumpteParticular::class);
        $this->missatge = '';
        $this->error = '';
        $this->validate([
            'professorRegularitzacio' => ['required', 'string', 'exists:profesores,dni'],
            'dataRegularitzacio' => ['required', 'date'],
            'tipusRegularitzacio' => ['required', 'in:lectiu,no_lectiu'],
        ]);

        try {
            app(AssumpteParticularService::class)->regularitzar(
                $this->professorRegularitzacio,
                $this->dataRegularitzacio,
                $this->tipusRegularitzacio,
                (string) authUser()->dni
            );
        } catch (AssumpteParticularException $exception) {
            $this->error = $exception->getMessage();
            return;
        }

        $this->reset(['professorRegularitzacio', 'dataRegularitzacio']);
        $this->tipusRegularitzacio = AssumpteParticular::TIPUS_LECTIU;
        $this->missatge = 'El dia ja gaudit s’ha incorporat a l’històric i al saldo.';
        $this->recarregar();
    }

    /** Renderitza la pantalla històrica de Direcció. */
    public function render(): View
    {
        return view('livewire.assumpte-particular-historic-panel');
    }
}
