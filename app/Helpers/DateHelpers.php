<?php

function Fecha($fecha)
{
    $hoy = new Date($fecha);
    return $hoy->toDateString();
}

function FechaString($fecha = null,$idioma=null)
{
    $fc1 = ($fecha==null)?new Date():(is_string($fecha))?New Date($fecha):$fecha;
    if (!isset($idioma)) $idioma = Session::get('lang'); 
    
    return $fc1->format('d') . ' de ' . ucwords(config("meses.$idioma.".$fc1->format('n'))) .
            ' de ' . $fc1->format('Y');
}

function Hoy($format=null)
{
    $fecha = new Date();
    return $format?$fecha->format($format):$fecha->toDateString();
}

function Ayer()
{
    $fecha = new Date();
    $fecha->subDay(1);
    return $fecha->toDateString();
}

function FechaPosterior($fecha1,$fecha2 = null){
    $fecha2 = is_null($fecha2)?new Date():is_string($fecha2)?new Date($fecha2):$fecha2;  
    $fecha1 = is_string($fecha1)?new Date($fecha1):$fecha1;
    return $fecha1 > $fecha2?$fecha1:$fecha2;
}

function haVencido($fecha)
{
    return Hoy() >= Fecha($fecha)?true:false;
}

function esMismoDia($ini, $fin)
{
    $fec1 = new Date($ini);
    $fec2 = new Date($fin);
    
    return $fec1->isSameDay($fec2)?true:false;   
}

function esMayor($ini, $fin)
{
    $fec1 = new Date($ini);
    $fec2 = new Date($fin);
    //dd($ini);
    return $fec1 > $fec2?true:false;
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
    return $fc1->day;
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
    Jenssegers\Date\Date::setlocale($idioma);
    $fc1 = new Jenssegers\Date\Date($fecha);
    return ucwords($fc1->format('F'));
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
    return config('constants.diaSemana.' . $fc1->format('N'));
}

/**
 * Devuelve la hora de una fecha
 * 
 * @param fecha
 * @return string
 */
function hora($fecha)
{
    $fc1 = new Date($fecha);
    return $fc1->format('H:i');
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
    $segi = substr($horaini, 6, 2);

    $horaf = substr($horafin, 0, 2);
    $minf = substr($horafin, 3, 2);
    $segf = substr($horafin, 6, 2);

    $ini = ((($horai * 60) * 60) + ($mini * 60) + $segi);
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
function Horas($hora)
{
    $horai = substr($hora, 0, 2);
    $mini = substr($hora, 3, 2);

    $horas = $horai + $mini / 60;

    return $horas;
}

