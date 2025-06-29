<?php

namespace Intranet\Livewire;

use Livewire\Component;
use Intranet\Entities\FctDay;
use Intranet\Entities\CalendariEscolar;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class FctCalendar extends Component
{
    public $alumnoFct;
    public $trams = [];
    public $monthlyCalendar = [];
    public $totalHours = 0;
    public $showConfigForm = true;
    public $allowNoLectiu = false;
    public $allowFestiu = false;

    public function mount($alumnoFct)
    {
        $this->alumnoFct = $alumnoFct;
        $this->allowNoLectiu = $alumnoFct->autorizacion ?? false;
        $this->allowFestiu = false;

        if (FctDay::where('alumno_fct_id', $alumnoFct->id)->exists()) {
            $this->showConfigForm = false;
            $this->loadCalendar();
        } else {
            $this->afegirTram();
        }
    }

    public function afegirTram()
    {
        $this->trams[] = [
            'inici' => '',
            'fi' => '',
            'hores' => '',
            'horesPerDia' => [
                'dilluns' => 0, 'dimarts' => 0, 'dimecres' => 0,
                'dijous' => 0, 'divendres' => 0, 'dissabte' => 0, 'diumenge' => 0,
            ],
        ];
    }

    public function createCalendar()
    {
        FctDay::where('alumno_fct_id', $this->alumnoFct->id)->delete();

        foreach ($this->trams as $tram) {
            if (!$tram['inici'] || !$tram['fi'] || !$tram['hores']) continue;

            $start = Carbon::parse($tram['inici']);
            $end = Carbon::parse($tram['fi']);
            $remaining = floatval($tram['hores']);

            for ($date = $start->copy(); $date->lte($end) && $remaining > 0; $date->addDay()) {
                if (
                    (!$this->allowNoLectiu && !CalendariEscolar::esLectiu($date)) ||
                    (!$this->allowFestiu && CalendariEscolar::esFestiu($date))
                ) {
                    continue;
                }

                $dayName = $date->locale('ca')->isoFormat('dddd');
                $hores = $tram['horesPerDia'][$dayName] ?? 0;
                if ($hores > $remaining) $hores = $remaining;

                if ($hores > 0) {
                    FctDay::create([
                        'alumno_fct_id' => $this->alumnoFct->id,
                        'dia' => $date->toDateString(),
                        'hores_previstes' => $hores,
                    ]);
                    $remaining -= $hores;
                }
            }
        }

        $this->showConfigForm = false;
        $this->loadCalendar();
    }

    public function loadCalendar()
    {
        $this->monthlyCalendar = FctDay::where('alumno_fct_id', $this->alumnoFct->id)
            ->orderBy('dia')
            ->get()
            ->map(function ($day) {
                $dia = Carbon::parse($day->dia);
                return [
                    'id' => $day->id,
                    'dia' => $day->dia,
                    'hores_previstes' => $day->hores_previstes,
                    'mes' => Carbon::parse($day->dia)->format('F'),
                    'dia_numero' => $dia->day,
                    'festiu' => CalendariEscolar::esFestiu($dia),
                    'lectiu' => CalendariEscolar::esLectiu($dia),
                ];
            })->groupBy('mes')->toArray();

        $this->totalHours = FctDay::where('alumno_fct_id', $this->alumnoFct->id)->sum('hores_previstes');
    }

    public function deleteCalendar()
    {
        FctDay::where('alumno_fct_id', $this->alumnoFct->id)->delete();
        $this->trams = [];
        $this->afegirTram();
        $this->showConfigForm = true;
        $this->monthlyCalendar = [];
        $this->totalHours = 0;
    }

    public function updateDay($id, $hours)
    {
        if ($day = FctDay::find($id)) {
            $day->update(['hores_previstes' => $hours]);
            $this->loadCalendar();
        }
    }

    public function render()
    {
        return view('livewire.fct-calendar', [
            'monthlyCalendar' => $this->monthlyCalendar,
            'totalHours' => $this->totalHours,
            'showConfigForm' => $this->showConfigForm,
        ]);
    }
    public function exportCalendarPdf()
    {
        $data = [
            'monthlyCalendar' => $this->monthlyCalendar,
            'totalHours' => $this->totalHours,
            'alumnoFct' => $this->alumnoFct
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('livewire.pdf.fct-calendar', $data);
        $nom = "calendari_" . $this->alumnoFct->Alumno->nia . ".pdf";

        return response()->stream(fn () => print($pdf->output()), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $nom . '"'
        ]);
    }

}
