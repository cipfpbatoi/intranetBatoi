<?php

declare(strict_types=1);

namespace Intranet\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Intranet\Application\AssumpteParticular\AssumpteParticularDireccionQueryService;
use Intranet\Application\AssumpteParticular\AssumpteParticularException;
use Intranet\Application\AssumpteParticular\AssumpteParticularService;
use Intranet\Application\Falta\FaltaAnulacioService;
use Intranet\Entities\AssumpteParticular;
use Intranet\Entities\Profesor;
use Livewire\Component;
use Livewire\Attributes\Locked;

/**
 * Històric de Direcció amb filtres, regularització i anul·lació de concessions.
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
    /** Identificador de la concessió seleccionada, protegit de canvis del client. */
    #[Locked]
    public ?int $anullarId = null;
    /** Resum informatiu de la concessió que es confirmarà. */
    public string $resumAnulacio = '';
    /** Motiu obligatori que es conservarà en la petició cancel·lada. */
    public string $motiuAnulacio = '';

    /**
     * Comprova l'accés exclusiu de Direcció i prepara els filtres.
     */
    public function mount(): void
    {
        $this->comprovarAccesDireccio();

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

    /** Obri la confirmació després de validar la concessió i la falta associada. */
    public function obrirAnulacio(int $id): void
    {
        $this->comprovarAccesDireccio();
        $this->reset(['missatge', 'error']);
        $this->cancelarAnulacio();
        $peticio = $this->peticioAnullable($id);
        $this->anullarId = $id;
        $this->resumAnulacio = sprintf(
            '%s — %s',
            trim($peticio->profesor?->fullName ?? $peticio->idProfesor),
            $peticio->data_gaudi->format('d/m/Y')
        );
    }

    /** Tanca la confirmació sense modificar la concessió. */
    public function cancelarAnulacio(): void
    {
        $this->reset(['anullarId', 'resumAnulacio', 'motiuAnulacio']);
        $this->resetValidation('motiuAnulacio');
    }

    /** Revalida i anul·la amb el mateix servei que el panell de faltes. */
    public function confirmarAnulacio(FaltaAnulacioService $service): void
    {
        $this->comprovarAccesDireccio();
        $this->reset(['missatge', 'error']);
        $this->motiuAnulacio = trim($this->motiuAnulacio);
        $this->validate(['motiuAnulacio' => ['required', 'string', 'max:1000']]);
        $peticio = $this->peticioAnullable($this->anullarId);
        $service->annullar((int) $peticio->falta_id, $this->motiuAnulacio, (string) authUser()->dni);
        $this->cancelarAnulacio();
        $this->missatge = __('assumptes_particulars.anulacio.completada');
        $this->recarregar();
    }

    /** Restringix també les accions Livewire posteriors al muntatge a Direcció. */
    private function comprovarAccesDireccio(): void
    {
        $user = authUser();
        abort_unless($user !== null && esRol($user->rol, config('roles.rol.direccion')), 403);
    }

    /** Recupera l'estat actual i impedix anul·lar altres orígens, cursos o faltes tancades. */
    private function peticioAnullable(?int $id): AssumpteParticular
    {
        $peticio = AssumpteParticular::query()->with(['profesor', 'falta'])->find($id);
        if (
            $peticio === null
            || $peticio->estat !== AssumpteParticular::ESTAT_AUTORITZADA
            || $peticio->origen !== AssumpteParticular::ORIGEN_SOLLICITUD
            || $peticio->curs !== app(AssumpteParticularService::class)->cursVigent()
            || $peticio->falta === null
        ) {
            throw ValidationException::withMessages([
                'motiuAnulacio' => __('assumptes_particulars.anulacio.no_disponible'),
            ]);
        }

        Gate::authorize('annul', $peticio->falta);

        return $peticio;
    }

    /** Renderitza la pantalla històrica de Direcció. */
    public function render(): View
    {
        return view('livewire.assumpte-particular-historic-panel');
    }
}
