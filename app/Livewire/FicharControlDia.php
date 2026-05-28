<?php

namespace Intranet\Livewire;

use Carbon\Carbon;
use Intranet\Application\Horario\HorarioService;
use Intranet\Application\Profesor\ProfesorService;
use Intranet\Entities\Falta_profesor;
use Intranet\Entities\Profesor;
use Livewire\Component;

class FicharControlDia extends Component
{
    public string $fecha = '';
    public string $fechaEsp = '';
    public array $rows = [];

    private ?ProfesorService $profesorService = null;
    private ?HorarioService $horarioService = null;

    public function mount(): void
    {
        $this->fecha = now()->format('Y-m-d');
        $this->refreshData();
    }

    public function updatedFecha(): void
    {
        $this->refreshData();
    }

    public function diaAnterior(): void
    {
        $date = Carbon::parse($this->fecha)->subDay();
        if ($date->dayOfWeekIso === 7) {
            $date->subDays(2);
        }
        $this->fecha = $date->format('Y-m-d');
        $this->refreshData();
    }

    public function diaSeguent(): void
    {
        $date = Carbon::parse($this->fecha)->addDay();
        if ($date->dayOfWeekIso === 6) {
            $date->addDays(2);
        }
        $this->fecha = $date->format('Y-m-d');
        $this->refreshData();
    }

    public function render()
    {
        return view('livewire.fichar-control-dia');
    }

    private function profesores(): ProfesorService
    {
        if ($this->profesorService === null) {
            $this->profesorService = app(ProfesorService::class);
        }

        return $this->profesorService;
    }

    private function horarios(): HorarioService
    {
        if ($this->horarioService === null) {
            $this->horarioService = app(HorarioService::class);
        }

        return $this->horarioService;
    }

    private function refreshData(): void
    {
        $this->fechaEsp = Carbon::parse($this->fecha)
            ->locale('ca')
            ->isoFormat('dddd, DD-MMM-YYYY');

        $profesores = $this->loadProfesoresForControlDia();

        $fichajesRaw = Falta_profesor::query()
            ->where('dia', $this->fecha)
            ->orderBy('entrada')
            ->get();

        $fichajes = [];
        foreach ($fichajesRaw as $fichaje) {
            $dni = (string) $fichaje->idProfesor;
            $line = (string) $fichaje->entrada . '->' . ((string) $fichaje->salida ?: '-');
            if (!isset($fichajes[$dni])) {
                $fichajes[$dni] = [];
            }
            $fichajes[$dni][] = $line;
        }

        $rows = [];
        foreach ($profesores as $profesor) {
            $horario = $this->horarios()->primeraByProfesorAndDateOrdered((string) $profesor->dni, $this->fecha);
            $horarioLabel = '';
            if (isset($horario->first()->desde)) {
                $horarioLabel = $horario->first()->desde . ' - ' . $horario->last()->hasta;
            }

            $dni = (string) $profesor->dni;
            $rows[] = [
                'dni' => $dni,
                'departamento' => (string) (optional($profesor->Departamento)->depcurt ?? ''),
                'nom' => trim((string) $profesor->apellido1 . ' ' . (string) $profesor->apellido2 . ', ' . (string) $profesor->nombre),
                'horario' => $horarioLabel,
                'fichajes' => $fichajes[$dni] ?? [],
            ];
        }

        $this->rows = $rows;
    }

    private function loadProfesoresForControlDia()
    {
        $profesores = Profesor::query()
            ->with('Departamento')
            ->Activo()
            ->orderBy('departamento')
            ->orderBy('apellido1')
            ->orderBy('apellido2')
            ->get();
        if ($profesores->isNotEmpty()) {
            return $profesores;
        }

        // Fallback per BBDD legacy: considerem actiu si no té baixa,
        // encara que el camp "activo" no estiga informat com 1.
        $profesores = Profesor::query()
            ->with('Departamento')
            ->where(function ($q): void {
                $q->whereNull('fecha_baja')
                    ->orWhere('fecha_baja', '')
                    ->orWhere('fecha_baja', '0000-00-00');
            })
            ->orderBy('departamento')
            ->orderBy('apellido1')
            ->orderBy('apellido2')
            ->get();
        if ($profesores->isNotEmpty()) {
            return $profesores;
        }

        // Últim fallback per no deixar la pantalla en blanc.
        return Profesor::query()
            ->with('Departamento')
            ->orderBy('departamento')
            ->orderBy('apellido1')
            ->orderBy('apellido2')
            ->get();
    }
}
