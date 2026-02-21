<?php

// segun horari on està el professor

use Intranet\Application\Horario\HorarioService;
use Intranet\Entities\Hora;

function horarioAhora($dni)
{
    $ahora = Jenssegers\Date\Date::now();
    $hora = sesion(Hora($ahora));
    $dia = config("auxiliares.diaSemana." . $ahora->format('w'));

    $horasDentro = app(HorarioService::class)->byProfesorDiaOrdered((string) $dni, (string) $dia);
    if (count($horasDentro) > 0) {
        if ($horasDentro->last()->sesion_orden < $hora) {
            return ['momento' => $horasDentro->last()->hasta, 'ahora' => trans('messages.generic.home')];
        }
        if ($horasDentro->first()->sesion_orden > $hora) {
            return ['momento' => $horasDentro->first()->desde, 'ahora' => trans('messages.generic.home')];
        }


        $horaActual = $horasDentro->where('sesion_orden', $hora)->first();
        if ($horaActual) {
            if (
                $horaActual->modulo != null
                && isset($horaActual->Modulo->literal)
                && isset($horaActual->Grupo)
                && isset($horaActual->Grupo->nombre)
            ) {
                return [
                    'momento' => $horaActual->Grupo->nombre,
                    'ahora' => $horaActual->Modulo->literal . ' (' . $horaActual->aula . ')'];
            }
            if ($horaActual->ocupacion != null && isset($horaActual->Ocupacion->nombre)) {
                return ['momento' => '', 'ahora' => $horaActual->Ocupacion->literal];
            }
        } else {
            return ['momento' => trans('messages.generic.patio'), 'ahora' => trans('messages.generic.patio')];
        }
    } else {
        return ['momento' => trans('messages.generic.notoday'), 'ahora' => trans('messages.generic.home')];
    }
    return null;
}



/**
 * Mira si el profesor esta en el instituto
 * @param profesor
 * @return boolean
 */
function estaDentro($profesor = null)
{
    $ultimo = Intranet\Entities\Falta_profesor::Hoy($profesor ?? authUser()->dni)
        ->latest('id')
        ->first();
    if ($profesor == null) {
        session(['ultimoFichaje' => $ultimo]);
    }
    return ($ultimo == null ? false : ($ultimo->salida == null ? true : false));
}

function Entrada()
{
    $registro = session('ultimoFichaje');
    if (isset($registro->entrada)) {
        return (substr($registro->entrada, 0, 5));
    }
    return null;
}

function Salida()
{
    $registro = session('ultimoFichaje');
    if (isset($registro->salida)) {
        return (substr($registro->salida, 0, 5));
    }
    return null;
}

/**
 * Mira si el profesor estaba en una hora concreta en el instituto
 *
 * @param dni profesor
 * @return boolean
 */
function estaInstituto($profesor, $dia, $hora)
{
    $fichadas = Intranet\Entities\Falta_profesor::haFichado($dia, $profesor)->get();
    foreach ($fichadas as $ficha) {
        if ($ficha->salida && $hora >= $ficha->entrada && $hora < $ficha->salida) {
            return true;
        }
    }
    return false;
}

function estaGuardia($idProfesor, $diaSemana, $sesion):bool
{
    return Intranet\Entities\Guardia::query()
        ->Profesor($idProfesor)
        ->DiaHora($diaSemana, $sesion)
        ->exists();
}

function profesoresGuardia($diaSemana, $sesion)
{
    return Intranet\Entities\Guardia::query()
        ->DiaHora($diaSemana, $sesion)
        ->select('idProfesor')
        ->get();
}
