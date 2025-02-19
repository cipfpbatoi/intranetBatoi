<?php
namespace Intranet\Livewire;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Intranet\Entities\CalendariEscolar;
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
    public $defaultHours = [];
    public $autorizacion = false;
    public $showConfigForm = true;
    public $daysToAdd = 1;
    public $allowFestiu = false;
    public $allowNoLectiu = false;

    public function mount(AlumnoFct $alumnoFct)
    {
        $this->alumnoFct = $alumnoFct;
        $this->allowNoLectiu = $alumnoFct->autorizacion;

        // Verificar si ja té calendari generat
        $existingDays = FctDay::where('alumno_fct_id', $this->alumnoFct->id)->count();
        if ($existingDays > 0) {
            $this->showConfigForm = false;
            $this->loadCalendar();
        } else {
            // Hores per defecte per cada dia de la setmana
             $this->setDefaultHoursFromCentro();
        }
    }

    public function createCalendar()
    {
        if (!$this->alumnoFct->desde) {
            Log::error("L'alumne FCT no té data d'inici definida.");
            return;
        }

        $startDate = Carbon::parse($this->alumnoFct->desde);
        $horesTotals = $this->alumnoFct->horas;
        $remainingHours = $horesTotals;

        for ($date = $startDate->copy(); $remainingHours > 0; $date->addDay()) {
            $dayName = $date->locale('ca')->isoFormat('dddd');

            if  ((!$this->allowNoLectiu && !CalendariEscolar::esLectiu($date))  ) {
                $horesDiaries = 0;
            } elseif (CalendariEscolar::esFestiu($date) && !$this->allowFestiu) {
                $horesDiaries = 0;
            } else {
                  $horesDiaries = $this->defaultHours[$dayName] ?? 8;
            }

            if ($remainingHours < $horesDiaries) {
                $horesDiaries = $remainingHours;
            }

            FctDay::create([
                'alumno_fct_id' => $this->alumnoFct->id,
                'dia' => $date->toDateString(),
                'hores_previstes' => $horesDiaries,
            ]);

            $remainingHours -= $horesDiaries;
        }

        $this->showConfigForm = false;
        $this->loadCalendar();
    }

    public function deleteCalendar()
    {
        FctDay::where('alumno_fct_id', $this->alumnoFct->id)->delete();
        $this->showConfigForm = true;
        $this->monthlyCalendar = [];
        $this->totalHours = 0;
    }

    public function loadCalendar()
    {
        $this->monthlyCalendar = FctDay::where('alumno_fct_id', $this->alumnoFct->id)
            ->orderBy('dia')
            ->get()
            ->map(function ($day) {
                return [
                    'id' => $day->id,
                    'dia' => $day->dia,
                    'hores_previstes' => $day->hores_previstes,
                    'mes' => Carbon::parse($day->dia)->format('F'),
                    'dia_numero' => Carbon::parse($day->dia)->format('j'),
                    'festiu' => \Intranet\Entities\CalendariEscolar::esFestiu(Carbon::parse($day->dia)),
                    'lectiu' =>  \Intranet\Entities\CalendariEscolar::esLectiu(Carbon::parse($day->dia)),
                ];
            })->groupBy('mes')->toArray();

        $this->totalHours = FctDay::where('alumno_fct_id', $this->alumnoFct->id)->sum('hores_previstes');
    }
    public function updateDay($id, $hours)
    {
        $day = FctDay::find($id);
        if ($day) {
            $day->update(['hores_previstes' => $hours]);
            $this->loadCalendar();
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
            $horesDiaries = 0;

            FctDay::updateOrCreate(
                ['alumno_fct_id' => $this->alumnoFct->id, 'dia' => $date->toDateString()],
                ['hores_previstes' => $horesDiaries]
            );
        }

        $this->loadCalendar();
    }

    public function setDefaultHoursFromCentro()
    {
        $horarios = $this->alumnoFct->Fct->Colaboracion->Centro->horarios ?? '';


        $this->defaultHours = [
            'dilluns' => 8, 'dimarts' => 8, 'dimecres' => 8, 'dijous' => 8, 'divendres' => 8,
            'dissabte' => 0, 'diumenge' => 0
        ];

        if (!$horarios) {
            return;
        }


        // Patró per a detectar horaris de qualsevol dia de la setmana
        preg_match_all('/([LMMXJVSD]): De (\d{1,2}:\d{2}) a (\d{1,2}:\d{2})(?: y de (\d{1,2}:\d{2}) a (\d{1,2}:\d{2}))?/', $horarios, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $diaLletra = $match[1]; // Lletra del dia (L, M, X, J, V, S, D)
            $matiInici = Carbon::createFromFormat('H:i', $match[2]);
            $matiFi = Carbon::createFromFormat('H:i', $match[3]);

             // Calcular hores del matí
            $horesMati = $matiFi->diffInMinutes($matiInici) / 60 ;

            $horesVesprada = 0;
            if (!empty($match[4]) && !empty($match[5])) {
                $vespradaInici = Carbon::createFromFormat('H:i', $match[4]);
                $vespradaFi = Carbon::createFromFormat('H:i', $match[5]);

                // Calcular hores de la vesprada
                $horesVesprada = $vespradaFi->diffInMinutes($vespradaInici) / 60;
            }

            // Assignar el total d'hores al dia corresponent
            $horesTotals = $horesMati + $horesVesprada;

            switch ($diaLletra) {
                case 'L': $this->defaultHours['dilluns'] = $horesTotals; break;
                case 'M': $this->defaultHours['dimarts'] = $horesTotals; break;
                case 'X': $this->defaultHours['dimecres'] = $horesTotals; break;
                case 'J': $this->defaultHours['dijous'] = $horesTotals; break;
                case 'V': $this->defaultHours['divendres'] = $horesTotals; break;
                case 'S': $this->defaultHours['dissabte'] = $horesTotals; break;
                case 'D': $this->defaultHours['diumenge'] = $horesTotals; break;
            }
        }
    }

    public function exportCalendarPdf()
    {
        $data = [
            'monthlyCalendar' => $this->monthlyCalendar,
            'totalHours' => $this->totalHours,
            'alumnoFct' => $this->alumnoFct
        ];

        $pdf = Pdf::loadView('livewire.pdf.fct-calendar', $data);
        $nom = "calendari_" . $this->alumnoFct->Alumno->nia . ".pdf";

        // Retorna el PDF en mode "inline" per mostrar-lo en el navegador
        return response()->stream(fn () => print($pdf->output()), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $nom . '"'
        ]);
    }

    public function render()
    {
        return view('livewire.fct-calendar', [
            'monthlyCalendar' => $this->monthlyCalendar,
            'totalHours' => $this->totalHours,
            'showConfigForm' => $this->showConfigForm,
        ]);
    }
}
