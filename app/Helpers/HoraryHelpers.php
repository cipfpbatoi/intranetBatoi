<?php

// segun horari on està el professor

function horarioAhora($dni)
{
    $ahora = Jenssegers\Date\Date::now();
    $hora = sesion(Hora($ahora));
    $dia = config("auxiliares.diaSemana." . $ahora->format('w'));

    $horasDentro = Intranet\Entities\Horario::Dia($dia)
            ->Profesor($dni)
            ->orderBy('sesion_orden')
            ->get();
    if (count($horasDentro) > 0) {
        if ($horasDentro->last()->sesion_orden < $hora) {
            return ['momento' => $horasDentro->last()->hasta, 'ahora' => trans('messages.generic.home')];
        }
        if ($horasDentro->first()->sesion_orden > $hora) {
            return ['momento' => $horasDentro->first()->desde, 'ahora' => trans('messages.generic.home')];
        }


        $horaActual = $horasDentro->where('sesion_orden', $hora)->first();
        if ($horaActual) {
            if ($horaActual->modulo != null && isset($horaActual->Modulo->cliteral) && $horaActual->Grupo->nombre) {
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

// coincidix element amb sesió actual
function coincideHorario($elemento, $sesion)
{
    if (esMismoDia($elemento->desde, $elemento->hasta)) {
        if (isset($elemento->dia_completo)) {
            return true;
        }
        $horas = getHorasAfectas($elemento);
        return (isset($horas[0]) && $sesion >= $horas[0] && $sesion <= $horas[count($horas) - 1])?
            true:false;
    } else {
        return true;
    }

}

/**
 * @param $elemento
 * @return array
 */
function getHorasAfectas($elemento): array
{
    if (isset($elemento->hora_ini)) {
        $horas = Intranet\Entities\Hora::horasAfectadas($elemento->hora_ini, $elemento->hora_fin);
    } else {
        $horas = Intranet\Entities\Hora::horasAfectadas(hora($elemento->desde), hora($elemento->hasta));
    }
    return $horas;
}

/**
 * Mira si el profesor esta en el instituto
 * @param profesor
 * @return boolean
 */
function estaDentro($profesor = null)
{
    $ultimo = Intranet\Entities\Falta_profesor::Hoy($profesor ?? authUser()->dni)->get()->last();
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

    return (Intranet\Entities\Guardia::where('idProfesor', $idProfesor)
        ->where('dia', $diaSemana)
        ->where('hora', $sesion)
        ->first())?true:false;
}

function profesoresGuardia($diaSemana, $sesion)
{
    return Intranet\Entities\Guardia::where('dia', $diaSemana)
        ->where('hora', $sesion)
        ->select('idProfesor')
        ->get();
}
