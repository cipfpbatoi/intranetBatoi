<?php
namespace Intranet\Livewire;

use Livewire\Component;
use Intranet\Entities\CalendariEscolar;
use Carbon\Carbon;

class CalendariComponent extends Component
{
    public $any;
    public $mes;
    public $dies = [];
    public $esdeveniments = [];

    public $seleccionat;
    public $tipus;
    public $esdeveniment;

    public function mount($any = null, $mes = null)
    {
        $this->any = $any ?? Carbon::now()->year;
        $this->mes = $mes ?? Carbon::now()->month;
        $this->carregarDies();
    }

    public function updatedMes()
    {
        $this->carregarDies(); // Recàrrega cada vegada que canvia el mes
    }

    public function canviarMes($increment)
    {
        $this->mes += $increment;
        if ($this->mes > 12) {
            $this->mes = 1;
            $this->any++;
        } elseif ($this->mes < 1) {
            $this->mes = 12;
            $this->any--;
        }
        $this->resetSeleccionat(); //
        $this->carregarDies(); // Actualitza els dies després del canvi de mes
    }

    public function carregarDies()
    {
        $primerDia = Carbon::create($this->any, $this->mes, 1);
        $ultimDia = $primerDia->copy()->endOfMonth();
        $dies = [];
        $this->esdeveniments = []; // Reset de la llista d'esdeveniments


        while ($primerDia->lte($ultimDia)) {
            $registre = CalendariEscolar::where('data', $primerDia->toDateString())->first();

            $dies[$primerDia->day] = [
                'data' => $primerDia->toDateString(),
                'tipus' => $registre->tipus ?? 'no definit',
                'esdeveniment' => $registre->esdeveniment ?? '',
            ];

            if (!empty($registre->esdeveniment)) {
                $this->esdeveniments[] = [
                    'dia' => $primerDia->day,
                    'esdeveniment' => $registre->esdeveniment,
                ];
            }

            $primerDia->addDay();
        }

        $this->dies = $dies;
        usort($this->esdeveniments, fn($a, $b) => $a['dia'] <=> $b['dia']);
    }

    public function seleccionarDia($dia)
    {
        $this->seleccionat = $dia;
        $registre = CalendariEscolar::where('data', "{$this->any}-{$this->mes}-{$dia}")->first();

        $this->tipus = $registre->tipus ?? 'lectiu';
        $this->esdeveniment = $registre->esdeveniment ?? '';
    }

    public function guardarCanvis()
    {
        CalendariEscolar::updateOrCreate(
            ['data' => "{$this->any}-{$this->mes}-{$this->seleccionat}"],
            ['tipus' => $this->tipus, 'esdeveniment' => $this->esdeveniment]
        );

        $this->carregarDies(); // Actualitza el calendari
        $this->resetSeleccionat();
        $this->dispatch ('tancarModal');
    }

    public function resetSeleccionat()
    {
        $this->seleccionat = null;
        $this->tipus = null;
        $this->esdeveniment = null;
        $this->dispatch ('tancarModal');
    }
    public function cancelarEdicio()
    {
        $this->resetSeleccionat();

    }


    public function render()
    {
        return view('livewire.calendari-component');
    }
}

