<?php

namespace Intranet\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PresenciaResumenService
{
    public function __construct(
        private int $GRACE_MINUTES = 10,          // tolerància general
        private bool $FLEX_NO_DOCENCIA = true,    // trams no docents més flexibles
        private bool $DOCENCIA_RIGIDA = true,     // trams docents estrictes
        private int $MIN_CHUNK_MINUTES = 3,       // fusionar intervals molt propers
        private int $NO_SALIDA_AFTER_MIN = 30     // després de quant temps marquem NO_SALIDA
    ) {}

    /**
     * Resum d'un dia per a un conjunt de professors.
     * $profes: col·lecció de professors (dni, nom, etc.) ja carregats, o null per carregar-los ací.
     */
    public function resumenDia(\DateTimeInterface|string $dia, ?Collection $profes = null): array
    {
        $date = Carbon::parse($dia)->startOfDay();
        $weekday = $this->weekdayLetter($date); // L,M,X,J,V

        // Professors en plantilla (actius)
        $profes ??= DB::table('profesores')
            ->select('dni','nombre','apellido1','apellido2','departamento')
            ->where('activo', 1)
            ->get();

        $dniList = $profes->pluck('dni')->all();

        // HORARI DEL DIA: horarios + horas
        $horariRows = DB::table('horarios as h')
            ->join('horas as ho', 'ho.codigo', '=', 'h.sesion_orden')
            ->select(
                'h.idProfesor as dni',
                'h.sesion_orden',
                'ho.hora_ini',
                'ho.hora_fin',
                'h.idGrupo',
                'h.ocupacion'
            )
            ->where('h.dia_semana', $weekday)
            ->whereIn('h.idProfesor', $dniList)
            ->orderBy('h.idProfesor')->orderBy('h.sesion_orden')
            ->get()
            ->groupBy('dni');

        // FITXATGES
        $fitxatges = DB::table('faltas_profesores')
            ->select('idProfesor as dni','entrada','salida')
            ->whereIn('idProfesor', $dniList)
            ->whereDate('dia', $date->toDateString())
            ->orderBy('idProfesor')->orderBy('entrada')
            ->get()
            ->groupBy('dni');

        // ACTIVITATS
        $actividades = DB::table('actividad_profesor as ap')
            ->join('actividades as a','a.id','=','ap.idActividad')
            ->select('ap.idProfesor as dni','a.desde','a.hasta','a.fueraCentro','a.extraescolar')
            ->whereIn('ap.idProfesor', $dniList)
            ->where(function($q) use ($date){
                $q->whereDate('a.desde','<=',$date->toDateString())
                  ->whereDate('a.hasta','>=',$date->toDateString());
            })
            ->get()
            ->groupBy('dni');

        // COMISSIONS
        $comisiones = DB::table('comisiones')
            ->select('idProfesor as dni','desde','hasta')
            ->whereIn('idProfesor', $dniList)
            ->where(function($q) use ($date){
                $q->whereDate('desde','<=',$date->toDateString())
                  ->whereDate('hasta','>=',$date->toDateString());
            })
            ->get()
            ->groupBy('dni');

        // FALTES (les tractem com a JUSTIFIED segons el teu criteri)
        $faltas = DB::table('faltas')
            ->select('idProfesor as dni','desde','hasta','hora_ini','hora_fin','dia_completo','estado','motivos')
            ->whereIn('idProfesor', $dniList)
            ->where(function($q) use ($date){
                $q->whereDate('desde','<=',$date->toDateString())
                  ->where(function($x) use ($date){
                      $x->whereNull('hasta')
                        ->orWhereDate('hasta','>=',$date->toDateString());
                  });
            })
            ->get()
            ->groupBy('dni');

        $out = [];

        foreach ($profes as $p) {
            $dni = $p->dni;

            // Horari planificat
            $plan = $this->buildPlannedSlotsFromDbRows($horariRows->get($dni) ?? collect(), $date);

            // Fitxatges
            $fichajesRows = $fitxatges->get($dni) ?? collect();
            $stays        = $this->buildStayIntervals($fichajesRows, $date);
            $hasOpenStay  = $this->hasOpenStay($fichajesRows);

            // Excepcions (activitats, comissions, faltes)
            $exc = $this->buildExceptionIntervals(
                $actividades->get($dni) ?? collect(),
                $comisiones->get($dni) ?? collect(),
                $faltas->get($dni) ?? collect(),
                $date
            );

            // Cobertura
            $coverage = $this->computeCoverage($plan, $stays, $exc);

            // Estat final (incloent NO_SALIDA)
            $status   = $this->decideStatus($coverage, $hasOpenStay, $plan, $date);

            $out[] = [
                'dni' => $dni,
                'nombre' => $p->nombre,
                'apellido1' => $p->apellido1,
                'apellido2' => $p->apellido2,
                'departamento' => $p->departamento,
                'planned_docencia_minutes' => $coverage['planned_docencia'],
                'planned_altres_minutes'   => $coverage['planned_altres'],
                'covered_docencia_minutes' => $coverage['covered_docencia'],
                'covered_altres_minutes'   => $coverage['covered_altres'],
                'in_center_minutes'        => $coverage['in_center'],
                'excepcions'               => $coverage['excepcions'],
                'status'                   => $status,
            ];
        }

        return $out;
    }

    // ---------- Helpers principals ----------

    private function weekdayLetter(Carbon $date): string
    {
        // dl=1..dg=7 -> L,M,X,J,V (la resta no s'usa)
        return ['','L','M','X','J','V','S','D'][$date->dayOfWeekIso] ?? 'L';
    }

    private function buildPlannedSlotsFromDbRows(Collection $rows, Carbon $day): array
    {
        $slots = [];
        foreach ($rows as $r) {
            if (!$r->hora_ini || !$r->hora_fin) continue;

            $from = Carbon::parse($day->toDateString().' '.$r->hora_ini.':00', 'Europe/Madrid');
            $to   = Carbon::parse($day->toDateString().' '.$r->hora_fin.':00', 'Europe/Madrid');

            // Criteri: si té idGrupo assumim DOCÈNCIA, si no ALTRES
            $tipo = $r->idGrupo ? 'DOCENCIA' : 'ALTRES';
            $slots[] = ['from'=>$from,'to'=>$to,'tipo'=>$tipo];
        }
        return $this->mergeTouchingByType($slots);
    }

    private function hasOpenStay(Collection $fichajesRows): bool
    {
        foreach ($fichajesRows as $r) {
            if ($r->entrada && (is_null($r->salida) || $r->salida === '')) {
                return true;
            }
        }
        return false;
    }

    private function buildStayIntervals(Collection $fichajes, Carbon $day): array
    {
        $dayStart = $day->copy()->startOfDay();
        $dayEndCap = $day->isToday()
            ? Carbon::now('Europe/Madrid')
            : $day->copy()->endOfDay();

        $intervals = [];

        foreach ($fichajes as $f) {
            if (!$f->entrada) continue;

            $from = Carbon::parse($day->toDateString().' '.($f->entrada ?? '00:00:00'), 'Europe/Madrid');
            $to   = $f->salida
                ? Carbon::parse($day->toDateString().' '.$f->salida, 'Europe/Madrid')
                : $dayEndCap->copy();

            if ($to->lessThan($from)) continue;

            $from = $this->clampToDay($from, $dayStart, $dayEndCap);
            $to   = $this->clampToDay($to,   $dayStart, $dayEndCap);
            if ($to->lessThanOrEqualTo($from)) continue;

            $intervals[] = ['from' => $from, 'to' => $to];
        }

        return $this->mergeOverlappingOrClose($intervals, $this->MIN_CHUNK_MINUTES);
    }

    private function buildExceptionIntervals(Collection $acts, Collection $coms, Collection $faltas, Carbon $day): array
    {
        $res = [];

        foreach ($acts as $a) {
            $res[] = [
                'from' => Carbon::parse($a->desde, 'Europe/Madrid'),
                'to'   => Carbon::parse($a->hasta, 'Europe/Madrid'),
                'tipo' => 'ACTIVITY',
            ];
        }

        foreach ($coms as $c) {
            $res[] = [
                'from' => Carbon::parse($c->desde, 'Europe/Madrid'),
                'to'   => Carbon::parse($c->hasta, 'Europe/Madrid'),
                'tipo' => 'COMMISSION',
            ];
        }

        foreach ($faltas as $f) {
            $from = $f->dia_completo
                ? $day->copy()->startOfDay()
                : Carbon::parse($day->toDateString().' '.($f->hora_ini ?? '00:00:00'), 'Europe/Madrid');
            $to   = $f->dia_completo
                ? $day->copy()->endOfDay()
                : Carbon::parse($day->toDateString().' '.($f->hora_fin ?? '23:59:59'), 'Europe/Madrid');

            $res[] = ['from'=>$from,'to'=>$to,'tipo'=>'JUSTIFIED'];
        }

        return $this->mergeByType($res);
    }

    private function computeCoverage(array $plan, array $stays, array $exc): array
    {
        $planned_doc = 0; $planned_alt = 0;
        $covered_doc = 0; $covered_alt = 0;
        $in_center   = 0;
        $excDetail   = [];

        // minuts al centre (només fitxatges)
        foreach ($stays as $s) {
            $in_center += $s['to']->diffInMinutes($s['from']);
        }

        foreach ($plan as $slot) {
            $mins   = $slot['to']->diffInMinutes($slot['from']);
            $isDoc  = ($slot['tipo'] === 'DOCENCIA');

            if ($isDoc) $planned_doc += $mins; else $planned_alt += $mins;

            $coverMinutes = 0;

            // excepcions cobreixen 100% el solapament
            foreach ($exc as $e) {
                $ov = $this->overlapMinutes($slot, $e);
                $coverMinutes += $ov;
                if ($ov > 0) {
                    $excDetail[] = ['tipo'=>$e['tipo'], 'minutes'=>$ov];
                }
            }

            // presència real amb tolerància
            $graceStart = $this->GRACE_MINUTES;
            $graceEnd   = $this->FLEX_NO_DOCENCIA && !$isDoc ? $this->GRACE_MINUTES : 0;
            if ($isDoc && $this->DOCENCIA_RIGIDA) {
                $graceStart = 0;
                $graceEnd   = 0;
            }

            foreach ($stays as $s) {
                $coverMinutes += $this->overlapWithGrace($slot, $s, $graceStart, $graceEnd);
            }

            $coverMinutes = min($coverMinutes, $mins);

            if ($isDoc) $covered_doc += $coverMinutes; else $covered_alt += $coverMinutes;
        }

        return [
            'planned_docencia' => $planned_doc,
            'planned_altres'   => $planned_alt,
            'covered_docencia' => $covered_doc,
            'covered_altres'   => $covered_alt,
            'in_center'        => $in_center,
            'excepcions'       => $excDetail,
        ];
    }

    private function decideStatus(array $c, bool $hasOpenStay, array $plan, Carbon $date): string
    {
        $planned = $c['planned_docencia'] + $c['planned_altres'];
        $covered = $c['covered_docencia'] + $c['covered_altres'];

        if ($planned === 0) return 'OFF';

        // NO_SALIDA: hi ha entrada sense eixida i el dia ja hauria d'estar tancat
        if ($hasOpenStay) {
            $lastEnd = null;
            foreach ($plan as $slot) {
                $lastEnd = $lastEnd ? max($lastEnd, $slot['to']) : $slot['to'];
            }
            if (!$date->isToday()) {
                return 'NO_SALIDA';
            } else {
                if ($lastEnd && Carbon::now('Europe/Madrid')->greaterThan(
                    $lastEnd->copy()->addMinutes($this->NO_SALIDA_AFTER_MIN)
                )) {
                    return 'NO_SALIDA';
                }
            }
        }

        if ($covered >= max(0, $planned - 1)) return 'OK';
        if ($c['covered_docencia'] === 0 && $c['covered_altres'] === 0) return 'ABSENT';
        return 'PARTIAL';
    }

    // ---------- Utils intervals ----------

    private function clampToDay(Carbon $t, Carbon $start, Carbon $end): Carbon
    {
        if ($t->lessThan($start)) return $start->copy();
        if ($t->greaterThan($end)) return $end->copy();
        return $t;
    }

    private function overlapMinutes(array $a, array $b): int
    {
        $from = max($a['from']->timestamp, $b['from']->timestamp);
        $to   = min($a['to']->timestamp,   $b['to']->timestamp);
        return max(0, intdiv($to - $from, 60));
    }

    private function overlapWithGrace(array $slot, array $stay, int $graceStart, int $graceEnd): int
    {
        $from = $slot['from']->copy()->subMinutes($graceStart);
        $to   = $slot['to']->copy()->addMinutes($graceEnd);
        return $this->overlapMinutes(['from'=>$from,'to'=>$to], $stay);
    }

    private function mergeOverlappingOrClose(array $intervals, int $gapMinutes): array
    {
        if (empty($intervals)) return [];
        usort($intervals, fn($a,$b)=>$a['from'] <=> $b['from']);

        $out  = [];
        $curr = $intervals[0];

        foreach (array_slice($intervals, 1) as $next) {
            $overlap = $next['from']->lte($curr['to']);
            $gap     = $curr['to']->diffInMinutes($next['from']);
            if ($overlap || $gap <= $gapMinutes) {
                if ($next['to']->gt($curr['to'])) {
                    $curr['to'] = $next['to'];
                }
            } else {
                $out[] = $curr;
                $curr  = $next;
            }
        }
        $out[] = $curr;

        return $out;
    }

    private function mergeTouchingByType(array $slots): array
    {
        if (empty($slots)) return [];
        usort($slots, fn($a,$b)=>[$a['tipo'],$a['from']] <=> [$b['tipo'],$b['from']]);
        $out = [];
        foreach ($slots as $s) {
            $k = count($out) - 1;
            if ($k >= 0 &&
                $out[$k]['tipo'] === $s['tipo'] &&
                $out[$k]['to']->equalTo($s['from'])) {
                $out[$k]['to'] = $s['to'];
            } else {
                $out[] = $s;
            }
        }
        return $out;
    }

    private function mergeByType(array $exc): array
    {
        if (empty($exc)) return [];
        usort($exc, fn($a,$b)=>[$a['tipo'],$a['from']] <=> [$b['tipo'],$b['from']]);
        $out = [];
        foreach ($exc as $e) {
            $k = count($out) - 1;
            if ($k >= 0 &&
                $out[$k]['tipo'] === $e['tipo'] &&
                $out[$k]['to']->gte($e['from']->copy()->subMinute())) {
                if ($e['to']->gt($out[$k]['to'])) {
                    $out[$k]['to'] = $e['to'];
                }
            } else {
                $out[] = $e;
            }
        }
        return $out;
    }
}
