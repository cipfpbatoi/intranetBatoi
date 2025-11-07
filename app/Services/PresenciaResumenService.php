<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PresenciaResumenService
{
    public function __construct(
        private int $GRACE_MINUTES = 10,       // tolerància (minuts)
        private bool $FLEX_NO_DOCENCIA = true, // flexibilitat en trams no docents
        private bool $DOCENCIA_RIGIDA = true,  // docència estricta
        private int $MIN_CHUNK_MINUTES = 3     // fusionar estades molt properes
    ) {}

    public function resumenDia(\DateTimeInterface|string $dia, ?Collection $profes = null): array
    {
        $date = Carbon::parse($dia)->startOfDay();
        $weekday = $this->weekdayLetter($date); // L,M,X,J,V

        // Professors en plantilla (actius)
        $profes ??= DB::table('profesores')
            ->select('dni','nombre','apellido1','apellido2','departamento')
            ->where('activo', 1) // si uses un altre escop, ajusta
            ->get();

        $dniList = $profes->pluck('dni')->all();

        // ---- HORARI DEL DIA ----
        // horarios (dia_semana, sesion_orden, idProfesor) + horas (codigo -> hora_ini/hora_fin)
        $horariRows = DB::table('horarios as h')
            ->join('horas as ho', 'ho.codigo', '=', 'h.sesion_orden')
            ->select('h.idProfesor as dni','h.sesion_orden','ho.hora_ini','ho.hora_fin','h.idGrupo','h.ocupacion')
            ->where('h.dia_semana', $weekday)
            ->whereIn('h.idProfesor', $dniList)
            ->orderBy('h.idProfesor')->orderBy('h.sesion_orden')
            ->get()
            ->groupBy('dni'); // cada profe, trams del dia
        // Nota: si vols decidir DOCÈNCIA/ALTRES: usa h.idGrupo/h.ocupacion per marcar tipus

        // ---- FITXATGES (estades reals) ----
        $fitxatges = DB::table('faltas_profesores')
            ->select('idProfesor as dni','entrada','salida')
            ->whereIn('idProfesor', $dniList)
            ->whereDate('dia', $date->toDateString())
            ->orderBy('idProfesor')->orderBy('entrada')
            ->get()
            ->groupBy('dni'); // parelles entrada/salida del dia

        // ---- ACTIVITATS assignades (cobertura) ----
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

        // ---- COMISSIONS (cobertura) ----
        $comisiones = DB::table('comisiones')
            ->select('idProfesor as dni','desde','hasta')
            ->whereIn('idProfesor', $dniList)
            ->where(function($q) use ($date){
                $q->whereDate('desde','<=',$date->toDateString())
                  ->whereDate('hasta','>=',$date->toDateString());
            })
            ->get()
            ->groupBy('dni');

        // ---- FALTES (algunes poden cobrir segons criteri) ----
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

            $plan = $this->buildPlannedSlotsFromDbRows($horariRows->get($dni) ?? collect(), $date);
            $stays = $this->buildStayIntervals($fitxatges->get($dni) ?? collect(), $date);
            $exc   = $this->buildExceptionIntervals(
                $actividades->get($dni) ?? collect(),
                $comisiones->get($dni) ?? collect(),
                $faltas->get($dni) ?? collect(),
                $date
            );

            $coverage = $this->computeCoverage($plan, $stays, $exc);
            $status   = $this->decideStatus($coverage);

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

    private function weekdayLetter(Carbon $date): string
    {
        // dl=1..dg=7 -> L,M,X,J,V
        return ['','L','M','X','J','V','S','D'][$date->dayOfWeekIso] ?? 'L';
    }

    private function buildPlannedSlotsFromDbRows(Collection $rows, Carbon $day): array
    {
        $slots = [];
        foreach ($rows as $r) {
            if (!$r->hora_ini || !$r->hora_fin) continue;
            $from = Carbon::parse($day->toDateString().' '.$r->hora_ini.':00');
            $to   = Carbon::parse($day->toDateString().' '.$r->hora_fin.':00');

            // Decideix tipus: si hi ha idGrupo → assumim DOCÈNCIA; si no, ALTRES (ajusta al teu criteri)
            $tipo = $r->idGrupo ? 'DOCENCIA' : 'ALTRES';
            // També pots usar $r->ocupacion per distingir guàrdies/reunions, etc.

            $slots[] = ['from'=>$from,'to'=>$to,'tipo'=>$tipo];
        }
        return $this->mergeTouchingByType($slots);
    }

    private function buildStayIntervals(Collection $rows, Carbon $day): array
    {
        $intervals = [];
        foreach ($rows as $f) {
            if (!$f->entrada) continue;
            $from = Carbon::parse($day->toDateString().' '.$f->entrada);
            $to   = $f->salida ? Carbon::parse($day->toDateString().' '.$f->salida) : $day->copy()->endOfDay();
            if ($to->lessThan($from)) continue;
            $intervals[] = ['from'=>$from,'to'=>$to];
        }
        return $this->mergeClose($intervals, $this->MIN_CHUNK_MINUTES);
    }

    private function buildExceptionIntervals(Collection $acts, Collection $coms, Collection $faltas, Carbon $day): array
    {
        $res = [];

        foreach ($acts as $a) {
            // Encara que siga fueraCentro/extraescolar, la contem com cobertura
            $res[] = [
                'from' => Carbon::parse($a->desde),
                'to'   => Carbon::parse($a->hasta),
                'tipo' => 'ACTIVITY',
            ];
        }

        foreach ($coms as $c) {
            $res[] = [
                'from' => Carbon::parse($c->desde),
                'to'   => Carbon::parse($c->hasta),
                'tipo' => 'COMMISSION',
            ];
        }

        foreach ($faltas as $f) {
            // Criteri: si dia complet o tram horari → considerem JUSTIFIED.
            // Pots filtrar per 'estado' o 'motivos' si voleu només alguns casos.
            $from = $f->dia_completo ? $day->copy()->startOfDay()
                                     : Carbon::parse($day->toDateString().' '.($f->hora_ini ?? '00:00:00'));
            $to   = $f->dia_completo ? $day->copy()->endOfDay()
                                     : Carbon::parse($day->toDateString().' '.($f->hora_fin ?? '23:59:59'));
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

        foreach ($stays as $s) $in_center += $s['to']->diffInMinutes($s['from']);

        foreach ($plan as $slot) {
            $mins   = $slot['to']->diffInMinutes($slot['from']);
            $isDoc  = ($slot['tipo'] === 'DOCENCIA');

            if ($isDoc) $planned_doc += $mins; else $planned_alt += $mins;

            $coverMinutes = 0;

            foreach ($exc as $e) {
                $ov = $this->overlapMinutes($slot, $e);
                $coverMinutes += $ov;
                if ($ov > 0) $excDetail[] = ['tipo'=>$e['tipo'], 'minutes'=>$ov];
            }

            // Toleràncies: docència estricta o no
            $graceStart = $this->GRACE_MINUTES;
            $graceEnd   = $this->FLEX_NO_DOCENCIA && !$isDoc ? $this->GRACE_MINUTES : 0;
            if ($isDoc && $this->DOCENCIA_RIGIDA) { $graceStart = 0; $graceEnd = 0; }

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

    private function decideStatus(array $c): string
    {
        $planned = $c['planned_docencia'] + $c['planned_altres'];
        $covered = $c['covered_docencia'] + $c['covered_altres'];

        if ($planned === 0) return 'OFF';
        if ($covered >= max(0, $planned - 1)) return 'OK';
        if ($c['covered_docencia'] === 0 && $c['covered_altres'] === 0) return 'ABSENT';
        return 'PARTIAL';
    }

    // --- utils d’intervals ---

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

    private function mergeClose(array $intervals, int $withinMinutes): array
    {
        if (empty($intervals)) return [];
        usort($intervals, fn($a,$b)=>$a['from']<=>$b['from']);
        $out = [$intervals[0]];
        for ($i=1;$i<count($intervals);$i++){
            $prev = &$out[count($out)-1];
            if ($intervals[$i]['from']->diffInMinutes($prev['to']) <= $withinMinutes) {
                if ($intervals[$i]['to']->greaterThan($prev['to'])) $prev['to'] = $intervals[$i]['to'];
            } else {
                $out[] = $intervals[$i];
            }
        }
        return $out;
    }

    private function mergeTouchingByType(array $slots): array
    {
        if (empty($slots)) return [];
        usort($slots, fn($a,$b)=>[$a['tipo'],$a['from']]<=>[$b['tipo'],$b['from']]);
        $out = [];
        foreach ($slots as $s) {
            $k = count($out)-1;
            if ($k>=0 && $out[$k]['tipo']===$s['tipo'] && $out[$k]['to']->equalTo($s['from'])) {
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
        usort($exc, fn($a,$b)=>[$a['tipo'],$a['from']]<=>[$b['tipo'],$b['from']]);
        $out = [];
        foreach ($exc as $e) {
            $k = count($out)-1;
            if ($k>=0 && $out[$k]['tipo']===$e['tipo'] && $out[$k]['to']->gte($e['from']->copy()->subMinute())) {
                if ($e['to']->gt($out[$k]['to'])) $out[$k]['to'] = $e['to'];
            } else {
                $out[] = $e;
            }
        }
        return $out;
    }
}
