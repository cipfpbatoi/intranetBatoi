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
    public $grid = [];

    public $seleccionat;
    public $tipus;
    public $esdeveniment;

    private function dataCompletada(int $dia): string
    {
        return Carbon::create($this->any, $this->mes, $dia)->toDateString();
    }

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
        $primerDia = Carbon::create($this->any, $this->mes, 1)->startOfDay();
        $ultimDia = $primerDia->copy()->endOfMonth();
        $dies = [];
        $this->esdeveniments = []; // Reset de la llista d'esdeveniments per mes
        // Carrega tots els registres del mes en una sola consulta per evitar retards
        $registres = CalendariEscolar::whereBetween('data', [
            $primerDia->toDateString(),
            $ultimDia->toDateString(),
        ])->get()->keyBy('data');

        $diaCursor = $primerDia->copy();
        while ($diaCursor->lte($ultimDia)) {
            $data = $diaCursor->toDateString();
            $registre = $registres[$data] ?? null;

            $dies[$diaCursor->day] = [
                'data' => $data,
                'tipus' => $registre->tipus ?? 'lectiu',
                'esdeveniment' => $registre->esdeveniment ?? '',
            ];

            if (!empty($registre?->esdeveniment)) {
                $this->esdeveniments[] = [
                    'dia' => $diaCursor->day,
                    'esdeveniment' => $registre->esdeveniment,
                ];
            }

            $diaCursor->addDay();
        }

        $this->dies = $dies; // Només dies del mes actual
        usort($this->esdeveniments, fn($a, $b) => $a['dia'] <=> $b['dia']);

        // Construeix una graella fixa (6 setmanes) començant dilluns, per evitar salts de files
        $iniciGraella = $primerDia->copy()->startOfWeek(Carbon::MONDAY);
        $finalGraella = $ultimDia->copy()->endOfWeek(Carbon::SUNDAY);
        $cursor = $iniciGraella->copy();
        $grid = [];

        while ($cursor->lte($finalGraella)) {
            $esMesActual = $cursor->month === $this->mes;
            $info = $esMesActual
                ? ($dies[$cursor->day] ?? [
                    'data' => $cursor->toDateString(),
                    'tipus' => 'lectiu',
                    'esdeveniment' => '',
                ])
                : [
                    'data' => $cursor->toDateString(),
                    'tipus' => 'fora',
                    'esdeveniment' => '',
                ];

            $grid[] = array_merge($info, [
                'numero' => $cursor->day,
                'es_mes_actual' => $esMesActual,
            ]);

            $cursor->addDay();
        }

        $this->grid = $grid;
    }

    public function seleccionarDia($dia)
    {
        if (!isset($this->dies[$dia])) {
            return;
        }

        $this->seleccionat = $dia;
        $registre = CalendariEscolar::where('data', $this->dataCompletada($dia))->first();

        $this->tipus = $registre->tipus ?? 'lectiu';
        $this->esdeveniment = $registre->esdeveniment ?? '';
    }

    public function guardarCanvis()
    {
        if (!$this->seleccionat) {
            return;
        }

        CalendariEscolar::updateOrCreate(
            ['data' => $this->dataCompletada($this->seleccionat)],
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
