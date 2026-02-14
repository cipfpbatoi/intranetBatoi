<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Jenssegers\Date\Date;

function periodePractiques($fecha = null)
{
    $inici = $fecha ? new Date($fecha) : new Date(hoy());
    $iniciLimit = new Date(config('curso.fct.2')['inici']);

    if ($inici <= $iniciLimit) {
        return 1;
    } else {
        return 2;
    }
}

function fechaCurta($fecha)
{
    $hoy = new Date($fecha);
    return $hoy->format("d/m");
}

function fecha($fecha)
{
    $hoy = new Date($fecha);
    return $hoy->toDateString();
}
function fechaSao($fecha)
{
    $descp = explode('/', $fecha);
    if (count($descp) !== 3) {
        return $fecha;
    }
    return $descp[2].'-'.$descp[1].'-'.$descp[0];
}

/**
 * Converteix una data curta (`dd-mm-yy` o `dd/mm/yy`) a format SQL (`YYYY-mm-dd`).
 *
 * @param string $fecha
 * @param string $separator
 * @return string
 */
function fechaInglesaCurta($fecha, $separator='-')
{
    $date = explode($separator, $fecha);
    if (count($date) !== 3) {
        return $fecha;
    }
    return '20'.$date[2].'-'.$date[1].'-'.$date[0];
}

function fechaInglesa($fecha)
{
    $hoy = new  Date($fecha);
    return $hoy->format('Y-m-d');
}

function fechaInglesaLarga($fecha)
{
    $hoy = new Date($fecha);
    return $hoy->format('Y-m-d H:i:s');
}

function buildFecha($fecha, $hora)
{
    $date = new Date($fecha);
    $str = $date->format('Y-m-d');
    return new \DateTime($str." ".$hora.":00");

}
function fechaString($fecha = null, $idioma = null)
{
    $fecha = is_string($fecha) ? new Jenssegers\Date\Date($fecha) : $fecha;
    $fc1 = $fecha ?? new Jenssegers\Date\Date();
    if (!isset($idioma)) {
        $idioma = Session::get('lang');
    }
    Jenssegers\Date\Date::setlocale($idioma);

    return $fc1->format('d') . ' de ' . $fc1->format('F') .
            ' de ' . $fc1->format('Y');
}

function hoy($format = null)
{
    $fecha = new Date();
    return $format ? $fecha->format($format) : $fecha->toDateString();
}

function ayer()
{
    $fecha = new Date();
    $fecha->subDay(1);
    return $fecha->toDateString();
}

function hace($days)
{
    $fecha = new Date();
    $fecha->subDay($days);
    return $fecha->format('Y-m-d');
}

function dentro($days)
{
    $fecha = new Date();
    $fecha->addDay($days);
    return $fecha->format('Y-m-d');
}

function manana($format = null)
{
    $fecha = new Date();
    $fecha->addDay(1);
    return $format ? $fecha->format($format) : $fecha->toDateString();
}

/**
 * Retorna una instància de data amb el dia de demà.
 *
 * @return Date
 */
function mananaDate()
{
    $fecha = new Date();
    $fecha->addDay(1);
    return $fecha;
}

function fechaPosterior($fecha1, $fecha2 = null)
{
    $fecha2 = is_string($fecha2) ? new Date($fecha2) : $fecha2;
    $fecha2 = $fecha2 ?? new Date();
    $fecha1 = is_string($fecha1) ? new Date($fecha1) : $fecha1;
    return $fecha1 > $fecha2 ? $fecha1 : $fecha2;
}


function haVencido($fecha)
{
    return hoy() >= fecha($fecha);
}

function vigente($fecha1, $fecha2)
{
    return haVencido($fecha1)&&!haVencido($fecha2);
}

function esMismoDia($ini, $fin)
{
    $fec1 = new Date($ini);
    $fec2 = new Date($fin);

    return $fec1->isSameDay($fec2);
}

function esMayor($ini, $fin)
{
    $fec1 = new Date($ini);
    $fec2 = new Date($fin);

    return $fec1 > $fec2;
}

/**
 * Devuelve el dia de una fecha
 *
 * @param fecha
 * @return integer
 */
function day($fecha)
{
    $fc1 = new Jenssegers\Date\Date($fecha);
    return str_pad($fc1->day, 2, '0', STR_PAD_LEFT);
}

/**
 * Devuelve el mes en letra de una fecha
 *
 * @param fecha
 * @return string
 */
function month($fecha)
{
    $idioma = Session::get('lang');
    $fc1 = new Jenssegers\Date\Date($fecha);
    Jenssegers\Date\Date::setlocale($idioma);
    return $fc1->format('F');
}

function mes($fecha)
{
    $fc1 = new Jenssegers\Date\Date($fecha);
    return str_pad($fc1->month, 2, '0', STR_PAD_LEFT);
}


function year($fecha)
{
    $fc1 = new Jenssegers\Date\Date($fecha);
    return $fc1->year;
}

function hour($fecha)
{
    $idioma = Session::get('lang');
    $fc1 = new Jenssegers\Date\Date($fecha);
    Jenssegers\Date\Date::setlocale($idioma);
    return $fc1->format('H:i');
}

/**
 * Devuelve el dia de la semana de una fecha
 *
 * @param fecha
 * @return char
 */
function nameDay($fecha)
{
    $fc1 = new Jenssegers\Date\Date($fecha);
    return config('auxiliares.diaSemana.' . $fc1->format('N'));
}

/**
 * Devuelve la hora de una fecha
 *
 * @param fecha
 * @return string
 */
function hora($fecha = null)
{
    $fc1 = $fecha ? new Date($fecha) : new Date();
    return $fc1->format('H:i');
}

/**
 * Retorna la sessió lectiva que correspon a una hora concreta.
 *
 * El resultat es cacheja per hora durant 1 minut.
 *
 * @param string $hora
 * @return int
 */
function sesion($hora)
{
    return Cache::remember('HoraSes:'.$hora, now()->addMinutes(1), function () use ($hora) {
            $now = \Intranet\Entities\Hora::where('hora_ini', '<=', $hora)->where('hora_fin', '>=', $hora)->first();
            return isset($now->codigo) ? $now->codigo : 0;
    });
}

/**
 * Resta dos hora
 *
 * @param horaini horafin
 * @return hora
 */
function restarHoras($horaini, $horafin)
{
    $horai = substr($horaini, 0, 2);
    $mini = substr($horaini, 3, 2);
    $segi = substr($horaini, 6, 2);

    $horaf = substr($horafin, 0, 2);
    $minf = substr($horafin, 3, 2);
    $segf = substr($horafin, 6, 2);

    $ini = ((($horai * 60) * 60) + ($mini * 60) + $segi);
    $fin = ((($horaf * 60) * 60) + ($minf * 60) + $segf);

    $dif = $fin - $ini;

    $difh = floor($dif / 3600);
    $difm = floor(($dif - ($difh * 3600)) / 60);
    $difs = $dif - ($difm * 60) - ($difh * 3600);
    return date("H:i:s", mktime($difh, $difm, $difs));
}

/**
 * Suma dos horas
 *
 * @param horaini horafin
 * @return hora
 */
function sumarHoras($horaini, $horafin)
{
    $horai = substr($horaini, 0, 2);
    $mini = substr($horaini, 3, 2);

    $horaf = substr($horafin, 0, 2);
    $minf = substr($horafin, 3, 2);
    $segf = substr($horafin, 6, 2);

    $ini = ((($horai * 60) * 60) + ($mini * 60));

    $fin = ((($horaf * 60) * 60) + ($minf * 60) + $segf);

    $dif = $fin + $ini;

    $difh = floor($dif / 3600);
    $difm = floor(($dif - ($difh * 3600)) / 60);
    $difs = $dif - ($difm * 60) - ($difh * 3600);

    return date("H:i:s", mktime($difh, $difm, $difs));
}

/**
 * Calcula hora en numero
 *
 * @param hora
 * @return integer
 */
function horas($hora)
{
    $horai = substr($hora, 0, 2);
    $mini = substr($hora, 3, 2);

    return $horai + $mini / 60;
}
