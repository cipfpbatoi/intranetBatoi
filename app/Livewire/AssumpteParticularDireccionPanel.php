<?php

declare(strict_types=1);

namespace Intranet\Livewire;

use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Intranet\Application\AssumpteParticular\AssumpteParticularDocumentService;
use Intranet\Application\AssumpteParticular\AssumpteParticularException;
use Intranet\Application\AssumpteParticular\AssumpteParticularDireccionQueryService;
use Intranet\Application\AssumpteParticular\AssumpteParticularService;
use Intranet\Application\AssumpteParticular\RubricaAssumpteParticularService;
use Intranet\Entities\AssumpteParticular;
use Intranet\Entities\Profesor;
use Livewire\Component;

/**
 * Panell informatiu de peticions d'assumptes particulars per a Direcció.
 */
class AssumpteParticularDireccionPanel extends Component
{
    /**
     * @var array<int, array<string, mixed>>
     */
    public array $grups = [];
    public string $filtreData = '';
    public ?int $peticioADenegar = null;
    public string $motiuDenegacio = '';
    public string $missatge = '';
    public string $error = '';
    public bool $potAutoritzar = false;
    public bool $teRubricaDirectora = false;

    /**
     * Comprova l'accés i carrega les peticions pendents.
     */
    public function mount(): void
    {
        $user = authUser();
        abort_unless(
            $user !== null && (
                esRol($user->rol, config('roles.rol.direccion'))
                || esRol($user->rol, config('roles.rol.administrador'))
            ),
            403
        );
        $this->potAutoritzar = esRol($user->rol, config('roles.rol.direccion'))
            && filled(config('avisos.director'));
        $directora = $this->potAutoritzar
            ? Profesor::query()->find((string) config('avisos.director'))
            : null;
        $this->teRubricaDirectora = $directora !== null
            && app(RubricaAssumpteParticularService::class)->exists($directora);
        $this->filtreData = CarbonImmutable::today()->addDays(7)->toDateString();

        $this->recarregar();
    }

    /**
     * Torna a calcular el panell amb les dades vigents.
     */
    public function recarregar(): void
    {
        $this->grups = app(AssumpteParticularDireccionQueryService::class)->grupsPendents(
            filled($this->filtreData) ? $this->filtreData : null
        );
    }

    /**
     * Aplica el filtre quan canvia la data seleccionada.
     */
    public function updatedFiltreData(): void
    {
        $this->resetValidation('filtreData');
        if (filled($this->filtreData)) {
            $this->validateOnly('filtreData', [
                'filtreData' => ['date_format:Y-m-d'],
            ]);
        }

        $this->recarregar();
    }

    /**
     * Neteja el filtre i torna a mostrar totes les dates pendents.
     */
    public function netejarFiltreData(): void
    {
        $this->filtreData = '';
        $this->resetValidation('filtreData');
        $this->recarregar();
    }

    /**
     * Mostra el formulari de denegació després de comprovar-ne l'autorització.
     */
    public function seleccionarDenegacio(int $id): void
    {
        $peticio = AssumpteParticular::query()->findOrFail($id);
        Gate::authorize('resolve', $peticio);

        $this->resetValidation('motiuDenegacio');
        $this->peticioADenegar = $id;
        $this->motiuDenegacio = '';
        $this->missatge = '';
        $this->error = '';
    }

    /**
     * Denega motivadament la petició seleccionada.
     */
    public function denegar(): void
    {
        $this->validate([
            'peticioADenegar' => ['required', 'integer'],
            'motiuDenegacio' => ['required', 'string', 'max:2000'],
        ], [
            'motiuDenegacio.required' => 'Cal indicar el motiu de la denegació.',
        ]);

        $peticio = AssumpteParticular::query()->findOrFail($this->peticioADenegar);
        Gate::authorize('resolve', $peticio);

        try {
            app(AssumpteParticularService::class)->denegar(
                (int) $peticio->id,
                (string) authUser()->dni,
                $this->motiuDenegacio
            );
        } catch (AssumpteParticularException $exception) {
            $this->error = $exception->getMessage();
            return;
        }

        $this->reset(['peticioADenegar', 'motiuDenegacio']);
        $this->missatge = 'La petició s’ha denegat correctament.';
        $this->error = '';
        $this->recarregar();
    }

    /**
     * Tanca el formulari de denegació sense modificar la petició.
     */
    public function cancelLarDenegacio(): void
    {
        $this->reset(['peticioADenegar', 'motiuDenegacio']);
        $this->resetValidation();
    }

    /**
     * Genera el document amb les dues rúbriques i autoritza la petició de forma indivisible.
     */
    public function autoritzar(int $id): void
    {
        $this->missatge = '';
        $this->error = '';
        $peticio = AssumpteParticular::query()->findOrFail($id);
        Gate::authorize('approve', $peticio);
        $directora = Profesor::query()->findOrFail((string) config('avisos.director'));

        try {
            $document = app(AssumpteParticularDocumentService::class)
                ->generarAutoritzada($peticio, $directora);
            app(AssumpteParticularService::class)->autoritzar(
                (int) $peticio->id,
                (string) authUser()->dni,
                $document
            );
        } catch (AssumpteParticularException $exception) {
            $this->error = $exception->getMessage();
            return;
        }

        $this->missatge = 'La petició s’ha autoritzat i arxivat correctament.';
        $this->recarregar();
    }

    /**
     * Renderitza el panell diari de Direcció.
     */
    public function render(): View
    {
        return view('livewire.assumpte-particular-direccion-panel');
    }
}
