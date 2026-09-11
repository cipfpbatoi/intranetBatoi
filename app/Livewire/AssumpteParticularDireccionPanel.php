<?php

declare(strict_types=1);

namespace Intranet\Livewire;

use Illuminate\Contracts\View\View;
use Intranet\Application\AssumpteParticular\AssumpteParticularDireccionQueryService;
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
     * Renderitza el panell diari de Direcció.
     */
    public function render(): View
    {
        return view('livewire.assumpte-particular-direccion-panel');
    }
}
