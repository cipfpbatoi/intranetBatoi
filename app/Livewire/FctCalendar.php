<?php
namespace Intranet\Livewire;

use Carbon\Carbon;
use Livewire\Component;
use Intranet\Entities\FctDay;
use Intranet\Entities\CalendariEscolar;
use Intranet\Entities\Alumno;
use Intranet\Entities\Colaboracion;
use Barryvdh\DomPDF\Facade\Pdf;
use ZipArchive;

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
    public $maxHours = 400;

    public function mount(Alumno $alumno)
    {
        $this->alumno = $alumno;
        $this->colaboraciones = Colaboracion::MiColaboracion()
            ->with('Centro:id,nombre')
            ->get()
            ->sortBy(fn($c) => mb_strtolower($c->Centro->nombre ?? ''))
            ->values();

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

        $assignades = 0; // acumulador d’hores creades

        foreach ($this->trams as $tram) {
            if (!$tram['inici'] || !$tram['fi']) continue;

            $inici = Carbon::parse($tram['inici']);
            $fi    = Carbon::parse($tram['fi']);

            for ($date = $inici->copy(); $date->lte($fi); $date->addDay()) {
                // Si ja hem arribat al límit, ix de TOTS els bucles
                if ($assignades >= $this->maxHours) break 2;

                $dow = $date->dayOfWeekIso;

                if (!$this->allowNoLectiu && CalendariEscolar::esNoLectiu($date)) continue;
                if (CalendariEscolar::esFestiu($date) && !$this->allowFestiu) continue;

                $horesPlan = (float)($tram['hores_setmana'][$dow] ?? 0);
                if ($horesPlan <= 0) continue;

                $restants = max($this->maxHours - $assignades, 0.0);
                if ($restants <= 0) break 2;

                // Assigna només el que queda fins al límit
                $horesAssignar = min($horesPlan, $restants);

                FctDay::create([
                    'nia'              => $this->alumno->nia,
                    'dia'              => $date->toDateString(),
                    'hores_previstes'  => $horesAssignar,
                    'colaboracion_id'  => $tram['colaboracion_id'] ?? null,
                ]);

                $assignades += $horesAssignar;

                // Si el dia planificat excedia el límit, parem igualment
                if ($horesAssignar < $horesPlan) break 2;
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
            $sorted = $fctDays->sortBy('dia')->values();
            $trams = [];
            $current = null;

            foreach ($sorted as $day) {
                $dow = Carbon::parse($day->dia)->dayOfWeekIso;
                $needsNewSegment = $current !== null && (
                    $day->colaboracion_id !== $current['colaboracion_id'] ||
                    ($current['hores_setmana'][$dow] > 0 && $current['hores_setmana'][$dow] !== $day->hores_previstes)
                );

                if ($needsNewSegment) {
                    $trams[] = $current;
                    $current = null;
                }

                if ($current === null) {
                    $current = [
                        'inici' => $day->dia,
                        'fi' => $day->dia,
                        'colaboracion_id' => $day->colaboracion_id,
                        'hores_setmana' => array_fill(1, 7, 0),
                    ];
                }

                $current['hores_setmana'][$dow] = $day->hores_previstes;
                $current['fi'] = $day->dia;
            }

            if ($current !== null) {
                $trams[] = $current;
            }

            $this->trams = $trams;
        }

        FctDay::where('nia', $this->alumno->nia)->delete();
    }

    public function loadCalendar()
    {
        $allDays = FctDay::where('nia', $this->alumno->nia)
            ->orderBy('dia')
            ->get();

        if ($allDays->isEmpty()) {
            $this->monthlyCalendar = [];
            $this->totalHours = 0;
            $this->colaboracionColors = [];
            $this->resumColaboracions = [];
            return;
        }

        // Map dia -> FctDay existent
        $byDate = $allDays->keyBy('dia');

        // Rang complet (del 1r al darrer mes)
        $start = Carbon::parse($allDays->min('dia'))->startOfMonth();
        $end   = Carbon::parse($allDays->max('dia'))->endOfMonth();

        $months = [];

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $dStr = $date->toDateString();

            $festiu   = CalendariEscolar::esFestiu($date)|| $date->isWeekend() ;
            $noLectiu = CalendariEscolar::esNoLectiu($date) ;

            $existing = $byDate->get($dStr);

            $row = [
                'id'              => $existing->id ?? null,
                'dia'             => $dStr,
                'colaboracion_id' => $existing->colaboracion_id ?? null,
                'hores_previstes' => $existing->hores_previstes ?? 0,
                'mes'             => $date->format('F Y'),
                'dia_numero'      => $date->day,
                'festiu'          => $festiu,
                'noLectiu'        => $noLectiu,
            ];

            $months[$row['mes']][] = $row;
        }

        $this->monthlyCalendar = $months;

        // Colors
        $colabIds = $allDays->whereNotNull('colaboracion_id')
            ->pluck('colaboracion_id')
            ->unique()
            ->values();

        $palette = ['#fce5cd', '#d9ead3', '#c9daf8', '#f4cccc', '#d0e0e3', '#ead1dc', '#fff2cc'];
        $this->colaboracionColors = [];
        foreach ($colabIds as $i => $id) {
            $this->colaboracionColors[$id] = $palette[$i % count($palette)];
        }

        // Resum i totals
        $this->totalHours = $allDays->sum('hores_previstes');
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

        $colaboracions = Colaboracion::with('Centro')->get();
        $colabLegend = $this->buildLegend($allDays, $colaboracions);
        $colorMap = collect($colabLegend)->mapWithKeys(fn ($item) => [$item['id'] => $item['color']])->toArray();

        // Agrupar dies per mes (inclosos any i mes) amb color per col·laboració
        $monthlyCalendar = $this->mapDaysToMonthlyCalendar($allDays, $colorMap);

        $documents = [];

        // 1. Vista principal (per a l'alumne)
        $documents[] = [
            'name' => "calendari_{$this->alumno->nia}_alumne.pdf",
            'content' => $this->renderPdfContent(
                $monthlyCalendar,
                $allDays->sum('hores_previstes'),
                "Calendari de FCT de l'alumne",
                $colabLegend
            )
        ];

        // 2. Per a cada col·laboració amb dies
        $colaboracionDays = $allDays->whereNotNull('colaboracion_id')->groupBy('colaboracion_id');

        foreach ($colaboracionDays as $colaboracionId => $dies) {
            $colaboracio = $colaboracions->firstWhere('id', $colaboracionId);
            $nomCentre = $colaboracio?->Centro?->nombre ?? 'Col·laboració #' . $colaboracionId;

            $calendar = $this->mapDaysToMonthlyCalendar($dies);

            $documents[] = [
                'name' => "calendari_{$this->alumno->nia}_$colaboracionId.pdf",
                'content' => $this->renderPdfContent(
                    $calendar,
                    $dies->sum('hores_previstes'),
                    "Calendari de FCT a $nomCentre"
                )
            ];
        }

        // Si només hi ha un document, enviem el PDF directe
        if (count($documents) === 1) {
            $doc = $documents[0];
            return response()->stream(fn () => print($doc['content']), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $doc['name'] . '"'
            ]);
        }

        // Generem un ZIP amb tots els PDFs (alumne + empreses)
        $zipPath = tempnam(sys_get_temp_dir(), 'fct_cal_');
        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            abort(500, 'No s\'ha pogut crear el fitxer ZIP del calendari.');
        }

        foreach ($documents as $doc) {
            $zip->addFromString($doc['name'], $doc['content']);
        }
        $zip->close();

        $zipName = "calendari_" . $this->alumno->nia . ".zip";
        return response()->download($zipPath, $zipName)->deleteFileAfterSend(true);
    }

    /**
     * Retorna el calendari agrupat per mes amb any inclòs per evitar desquadres.
     */
    private function mapDaysToMonthlyCalendar($days, array $colorMap = []): array
    {
        return $days->map(function ($day) {
            $date = Carbon::parse($day->dia);
            return [
                'dia' => $day->dia,
                'hores_previstes' => $day->hores_previstes,
                'mes' => $date->format('Y-m'),
                'dia_numero' => $date->day,
                'colaboracion_id' => $day->colaboracion_id,
            ];
        })->map(function ($day) use ($colorMap) {
            if ($day['colaboracion_id'] && isset($colorMap[$day['colaboracion_id']])) {
                $day['color'] = $colorMap[$day['colaboracion_id']];
            }
            return $day;
        })->groupBy('mes')->toArray();
    }

    /**
     * Genera el contingut PDF per a un calendari concret.
     */
    private function renderPdfContent(array $monthlyCalendar, float $totalHours, string $titol, ?array $legend = null): string
    {
        $html = view('livewire.pdf.fct-calendar', [
            'monthlyCalendar' => $monthlyCalendar,
            'totalHours' => $totalHours,
            'alumnoFct' => $this->alumno,
            'titol' => $titol,
            'legend' => $legend,
        ])->render();

        return Pdf::loadHtml($html)->output();
    }

    private function buildLegend($days, $colaboracions): array
    {
        $colabIds = $days->whereNotNull('colaboracion_id')->pluck('colaboracion_id')->unique()->values();
        if ($colabIds->isEmpty()) {
            return [];
        }

        $palette = ['#fce5cd', '#d9ead3', '#c9daf8', '#f4cccc', '#d0e0e3', '#ead1dc', '#fff2cc'];
        $legend = [];

        foreach ($colabIds as $i => $id) {
            $colaboracio = $colaboracions->firstWhere('id', $id);
            $nom = $colaboracio && $colaboracio->Centro ? $colaboracio->Centro->nombre : 'Col·laboració #' . $id;
            $legend[] = [
                'id' => $id,
                'nom' => $nom,
                'color' => $palette[$i % count($palette)],
            ];
        }

        return $legend;
    }


    public function render()
    {
        return view('livewire.fct-calendar');
    }
}
