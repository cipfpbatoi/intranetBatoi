<?php
namespace Intranet\Livewire;

use Carbon\Carbon;
use Livewire\Component;
use Intranet\Entities\FctDay;
use Intranet\Entities\CalendariEscolar;
use Intranet\Entities\Alumno;
use Intranet\Entities\Colaboracion;
use Barryvdh\DomPDF\Facade\Pdf;

class FctCalendar extends Component
{
    public $alumno;
    public $colaboraciones = [];
    public $trams = [];
    public $monthlyCalendar = [];
    public $totalHours = 0;
    public $showConfigForm = true;
    public $allowFestiu = false;
    public $allowNoLectiu = false;
    public $colaboracionColors = [];
    public $resumColaboracions = [];

    public function mount(Alumno $alumno)
    {
        $this->alumno = $alumno;
        $this->colaboraciones = Colaboracion::MiColaboracion()
            ->with('Centro:id,nombre')->get();

        $this->allowNoLectiu = false;

        if (FctDay::where('nia', $alumno->nia)->exists()) {
            $this->showConfigForm = false;
            $this->loadCalendar();
        } else {
            $this->trams = [[
                'inici' => null,
                'fi' => null,
                'colaboracion_id' => null,
                'hores_setmana' => array_fill(1, 7, 0),
            ]];
        }
    }

    public function addTram()
    {
        $this->trams[] = [
            'inici' => null,
            'fi' => null,
            'colaboracion_id' => null,
            'hores_setmana' => array_fill(1, 7, 0)
        ];
    }

    public function removeTram($index)
    {
        unset($this->trams[$index]);
        $this->trams = array_values($this->trams);
    }

    public function createCalendar()
    {
        FctDay::where('nia', $this->alumno->nia)->delete();

        foreach ($this->trams as $tram) {
            if (!$tram['inici'] || !$tram['fi']) continue;

            $inici = Carbon::parse($tram['inici']);
            $fi = Carbon::parse($tram['fi']);

            for ($date = $inici->copy(); $date->lte($fi); $date->addDay()) {
                $dow = $date->dayOfWeekIso;

                if (!$this->allowNoLectiu && !CalendariEscolar::esLectiu($date)) continue;
                if (CalendariEscolar::esFestiu($date) && !$this->allowFestiu) continue;

                $hores = $tram['hores_setmana'][$dow] ?? 0;
                if ($hores > 0) {
                    FctDay::create([
                        'nia' => $this->alumno->nia,
                        'dia' => $date->toDateString(),
                        'hores_previstes' => $hores,
                        'colaboracion_id' => $tram['colaboracion_id'] ?? null,
                    ]);
                }
            }
        }

        $this->loadCalendar();
        $this->showConfigForm = false;
    }

    public function deleteCalendar()
    {
        $this->monthlyCalendar = [];
        $this->totalHours = 0;
        $this->showConfigForm = true;

        $fctDays = FctDay::where('nia', $this->alumno->nia)->get();
        if ($fctDays->isNotEmpty()) {
            $trams = $fctDays->groupBy(function ($day) {
                return ($day->colaboracion_id ?? 'null') . '-' . $day->created_at->format('Y-m-d');

            })->map(function ($group) {
                $hores = array_fill(1, 7, 0);
                foreach ($group as $day) {
                    $dow = Carbon::parse($day->dia)->dayOfWeekIso;
                    $hores[$dow] = $day->hores_previstes;
                }
                return [
                    'inici' => $group->first()->dia,
                    'fi' => $group->last()->dia,
                    'colaboracion_id' => $group->first()->colaboracion_id,
                    'hores_setmana' => $hores,
                ];
            })->values()->toArray();

            $this->trams = $trams;
        }

        FctDay::where('nia', $this->alumno->nia)->delete();
    }

    public function loadCalendar()
    {
        $this->monthlyCalendar = FctDay::where('nia', $this->alumno->nia)
            ->orderBy('dia')->get()->map(function ($day) {
                $date = Carbon::parse($day->dia);
                return [
                    'id' => $day->id,
                    'dia' => $day->dia,
                    'colaboracion_id' => $day->colaboracion_id,
                    'hores_previstes' => $day->hores_previstes,
                    'mes' => $date->format('F'),
                    'dia_numero' => $date->day,
                    'festiu' => CalendariEscolar::esFestiu($date),
                    'lectiu' => CalendariEscolar::esLectiu($date),
                ];
            })->groupBy('mes')->toArray();
        $colabIds = FctDay::where('nia', $this->alumno->nia)
            ->whereNotNull('colaboracion_id')
            ->distinct()
            ->pluck('colaboracion_id')
            ->values();

        $colors = ['#fce5cd', '#d9ead3', '#c9daf8', '#f4cccc', '#d0e0e3', '#ead1dc', '#fff2cc'];
        $this->colaboracionColors = [];

        foreach ($colabIds as $i => $id) {
            $this->colaboracionColors[$id] = $colors[$i % count($colors)];
        }

        $this->totalHours = FctDay::where('nia', $this->alumno->nia)->sum('hores_previstes');
        $colaboraciones = Colaboracion::with('Centro')->get();

        $this->resumColaboracions = FctDay::selectRaw('colaboracion_id, SUM(hores_previstes) as total')
            ->where('nia', $this->alumno->nia)
            ->groupBy('colaboracion_id')
            ->pluck('total', 'colaboracion_id')
            ->mapWithKeys(function ($hores, $id) use ($colaboraciones) {
                if ($id === null) {
                    return ['null' => [
                        'nom' => 'Sense col·laboració',
                        'hores' => $hores,
                        'color' => '#eeeeee'
                    ]];
                }

                $colaboracio = $colaboraciones->firstWhere('id', $id);
                $nom = $colaboracio && $colaboracio->Centro ? $colaboracio->Centro->nombre : 'Col·laboració #' . $id;

                return [$id => [
                    'nom' => $nom,
                    'hores' => $hores,
                    'color' => $this->colaboracionColors[$id] ?? '#eeeeee'
                ]];
            })->toArray();

    }

    public function updateDay($id, $hours)
    {
        if ($day = FctDay::find($id)) {
            $day->update(['hores_previstes' => $hours]);
            $this->loadCalendar();
        }
    }

    public function exportCalendarPdf()
    {
        // Dies totals
        $allDays = FctDay::where('nia', $this->alumno->nia)
            ->orderBy('dia')->get();

        // Agrupar dies per mes
        $monthlyCalendar = $allDays->map(function ($day) {
            $date = Carbon::parse($day->dia);
            return [
                'dia' => $day->dia,
                'hores_previstes' => $day->hores_previstes,
                'mes' => $date->format('F'),
                'dia_numero' => $date->day,
            ];
        })->groupBy('mes')->toArray();

        $views = [];

        // 1. Vista principal (per a l'alumne)
        $views[] = view('livewire.pdf.fct-calendar', [
            'monthlyCalendar' => $monthlyCalendar,
            'totalHours' => $allDays->sum('hores_previstes'),
            'alumnoFct' => $this->alumno,
            'titol' => 'Calendari de FCT de l\'alumne',
        ])->render();

        // 2. Per a cada col·laboració amb dies
        $colaboracions = Colaboracion::with('Centro')->get();

        $colaboracionDays = $allDays->whereNotNull('colaboracion_id')->groupBy('colaboracion_id');

        foreach ($colaboracionDays as $colaboracionId => $dies) {
            $colaboracio = $colaboracions->firstWhere('id', $colaboracionId);
            $nomCentre = $colaboracio?->Centro?->nombre ?? 'Col·laboració #' . $colaboracionId;

            $calendar = $dies->map(function ($day) {
                $date = Carbon::parse($day->dia);
                return [
                    'dia' => $day->dia,
                    'hores_previstes' => $day->hores_previstes,
                    'mes' => $date->format('F'),
                    'dia_numero' => $date->day,
                ];
            })->groupBy('mes')->toArray();

            $views[] = view('livewire.pdf.fct-calendar', [
                'monthlyCalendar' => $calendar,
                'totalHours' => $dies->sum('hores_previstes'),
                'alumnoFct' => $this->alumno,
                'titol' => "Calendari de FCT a $nomCentre",
            ])->render();
        }

        // Combinar totes les vistes en un PDF
        $pdf = Pdf::loadHtml(implode('<div class="page-break"></div>', $views));
        $nom = "calendari_" . $this->alumno->nia . ".pdf";

        return response()->stream(fn () => print($pdf->output()), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $nom . '"'
        ]);
    }


    public function render()
    {
        return view('livewire.fct-calendar');
    }
}
