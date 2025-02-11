<?php

namespace Intranet\Livewire;

use Illuminate\Support\Facades\Log;
use Intranet\Entities\FctDay;
use Livewire\Component;
use Intranet\Entities\AlumnoFct;
use Carbon\Carbon;

class FctCalendar extends Component
{
    public $alumnoFct;
    public $calendar = [];
    public $monthlyCalendar = [];
    public $totalHours = 0;
    public $defaultHours = [
        'Dilluns' => 8,
        'Dimarts' => 8,
        'Dimecres' => 8,
        'Dijous' => 8,
        'Divendres' => 8,
        'Dissabte' => 0,
        'Diumenge' => 0,
    ];
    public $daysToAdd = 5; // Número de dies per defecte a afegir

    public function mount(AlumnoFct $alumnoFct)
    {
        $this->alumnoFct = $alumnoFct;
        $this->initializeCalendar();
    }

    public function initializeCalendar()
    {
        $existingDays = FctDay::where('alumno_fct_id', $this->alumnoFct->id)->count();

        if ($existingDays == 0) {
            Log::info("Generant calendari inicial per AlumnoFct ID: {$this->alumnoFct->id}");
            $this->generateInitialCalendar();
        }

        $this->loadCalendar();
    }

    public function generateInitialCalendar()
    {
        if (!$this->alumnoFct->desde) {
            Log::error("L'alumne FCT no té data d'inici definida.");
            return;
        }

        $startDate = Carbon::parse($this->alumnoFct->desde);
        $horesTotals = $this->alumnoFct->horas;

        Log::info("Data d'inici: {$startDate->toDateString()}, Hores totals: {$horesTotals}");

        $remainingHours = $horesTotals;
        for ($date = $startDate->copy(); $remainingHours > 0; $date->addDay()) {
            $dayName = $date->locale('ca')->isoFormat('dddd');

            $horesDiaries = $this->defaultHours[$dayName] ?? 8;

            if ($remainingHours < $horesDiaries) {
                $horesDiaries = $remainingHours;
            }

            FctDay::updateOrCreate(
                ['alumno_fct_id' => $this->alumnoFct->id, 'dia' => $date->toDateString()],
                ['hores_previstes' => $horesDiaries]
            );

            Log::info("Afegit dia: {$date->toDateString()} - Hores: {$horesDiaries}");

            $remainingHours -= $horesDiaries;
        }

        // Afegir 2 setmanes de marge amb hores a zero
        for ($i = 0; $i < 14; $i++) {
            $date = $startDate->copy()->addDays($i + $remainingHours);
            FctDay::updateOrCreate(
                ['alumno_fct_id' => $this->alumnoFct->id, 'dia' => $date->toDateString()],
                ['hores_previstes' => 0]
            );
        }
    }

    public function addDays()
    {
        $lastDay = FctDay::where('alumno_fct_id', $this->alumnoFct->id)->orderBy('dia', 'desc')->first();

        if (!$lastDay) {
            return;
        }

        $lastDate = Carbon::parse($lastDay->dia);
        for ($i = 1; $i <= $this->daysToAdd; $i++) {
            $date = $lastDate->copy()->addDays($i);
            $dayName = $date->locale('ca')->isoFormat('dddd');
            $horesDiaries = $this->defaultHours[$dayName] ?? 8;

            FctDay::updateOrCreate(
                ['alumno_fct_id' => $this->alumnoFct->id, 'dia' => $date->toDateString()],
                ['hores_previstes' => $horesDiaries]
            );
        }

        $this->loadCalendar();
    }

    public function updateDay($id, $hours)
    {
        $day = FctDay::find($id);
        if ($day) {
            $day->update(['hores_previstes' => $hours]);
            $this->loadCalendar();
        }
    }

    public function updateDefaultHours($day, $hours)
    {
        if (!array_key_exists($day, $this->defaultHours)) {
            return;
        }

        $this->defaultHours[$day] = $hours;

        FctDay::where('alumno_fct_id', $this->alumnoFct->id)
            ->whereRaw("WEEKDAY(dia) = ?", [array_search($day, array_keys($this->defaultHours))])
            ->update(['hores_previstes' => $hours]);

        $this->loadCalendar();
    }

    public function loadCalendar()
    {
        $calendarData = FctDay::where('alumno_fct_id', $this->alumnoFct->id)
            ->orderBy('dia')
            ->get()
            ->map(function ($day) {
                return [
                    'id' => $day->id,
                    'dia' => $day->dia,
                    'hores_previstes' => $day->hores_previstes,
                    'mes' => Carbon::parse($day->dia)->format('F'),
                    'dia_numero' => Carbon::parse($day->dia)->format('j'),
                    'weekend' => Carbon::parse($day->dia)->isWeekend(),
                ];
            })->groupBy('mes')->toArray();

        $this->monthlyCalendar = $calendarData;
        $this->totalHours = FctDay::where('alumno_fct_id', $this->alumnoFct->id)->sum('hores_previstes');
    }

    public function render()
    {
        return view('livewire.fct-calendar', [
            'monthlyCalendar' => $this->monthlyCalendar,
            'totalHours' => $this->totalHours,
            'defaultHours' => $this->defaultHours,
        ]);
    }
}
