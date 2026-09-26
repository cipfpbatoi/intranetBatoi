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
    /** @var array<int, array<string, mixed>> */
    public array $historial = [];
    /** @var array<string, string> */
    public array $professors = [];
    public string $filtreData = '';
    public string $professorRegularitzacio = '';
    public string $dataRegularitzacio = '';
    public string $tipusRegularitzacio = AssumpteParticular::TIPUS_LECTIU;
    public ?int $peticioADenegar = null;
    public string $motiuDenegacio = '';
    public string $missatge = '';
    public string $error = '';
    public bool $potAutoritzar = false;
    public bool $teRubricaDirectora = false;
    public bool $potRegularitzar = false;

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
        $this->potRegularitzar = esRol($user->rol, config('roles.rol.direccion'));
        $directora = $this->potAutoritzar
            ? Profesor::query()->find((string) config('avisos.director'))
            : null;
        $this->teRubricaDirectora = $directora !== null
            && app(RubricaAssumpteParticularService::class)->exists($directora);
        $this->filtreData = CarbonImmutable::today()->addDays(7)->toDateString();
        if ($this->potRegularitzar) {
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
        }

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
        $this->historial = app(AssumpteParticularDireccionQueryService::class)->autoritzades(
            app(AssumpteParticularService::class)->cursVigent()
        );
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
