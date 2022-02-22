<?php

namespace Intranet\Services;

use Intranet\Componentes\Mensaje;
use Intranet\Entities\Grupo;
use Intranet\Entities\Hora;
use Intranet\Entities\Horario;

use Intranet\Jobs\SendEmail;
use Styde\Html\Facades\Alert;
use function hora, esMismoDia, nameDay;

class AdviseTeacher
{
    public static function exec($elemento, $mensaje = null, $idEmisor = null, $emisor = null)
    {
        $mensaje = $mensaje ? $mensaje : "No estaré en el centre des de " . $elemento->desde . " fins " . $elemento->hasta;
        $idEmisor = $idEmisor ? $idEmisor : $elemento->idProfesor;

        if (count($grupos = self::gruposAfectados($elemento, $idEmisor)->toArray()) == 0) {
            return;
        }

        foreach (self::profesoresAfectados($grupos, $idEmisor) as $profesor) {
            Mensaje::send($profesor->idProfesor, $mensaje, '#', $emisor);
        }
    }

    private static function profesoresAfectados($grupos, $emisor)
    {
        return Horario::distinct()
            ->select('idProfesor')
            ->whereIn('idGrupo', $grupos)
            ->where('idProfesor', '<>', $emisor)
            ->get();
    }

    public static function gruposAfectados($elemento, $idProfesor)
    {
        if (!esMismoDia($elemento->desde, $elemento->hasta)) {
            return (Horario::distinct()
                ->select('idGrupo')
                ->Profesor($idProfesor)
                ->whereNotNull('idGrupo')
                ->get());
        }

        $dia_semana = nameDay($elemento->desde);
        if (count($horas = self::horasAfectadas($elemento))) {
            return (Horario::distinct()
                ->select('idGrupo')
                ->Profesor($idProfesor)
                ->Dia($dia_semana)
                ->whereNotNull('idGrupo')
                ->whereIn('sesion_orden', $horas)
                ->get());
        }

        return collect();
    }

    private static function horasAfectadas($elemento)
    {
        if (!isset($elemento->dia_completo)) {
            return Hora::horasAfectadas(hora($elemento->desde), hora($elemento->hasta));
        }
        if ($elemento->dia_completo) {
            return Hora::horasAfectadas('07:00', '23:00');
        }
        return Hora::horasAfectadas($elemento->hora_ini, $elemento->hora_fin);
    }

    public static function sendEmailTutor($elemento)
    {
        $idEmisor = $elemento->idProfesor;
        foreach (self::gruposAfectados($elemento, $idEmisor)->toArray() as $grupos){
            foreach ($grupos as $item) {
                $grupo = Grupo::find($item);
                $correoTutor = $grupo->Tutor->Sustituye->email ?? $grupo->Tutor->email;
                $correoDireccion = 'faltes@cipfpbatoi.es';
                $remitente =  ['nombre'=>'Caporalia','email'=>'faltes@cipfpbatoi.es'];

                SendEmail::dispatch($correoTutor,$remitente, 'email.faltaProfesor', $elemento);
                SendEmail::dispatch($correoDireccion, $remitente, 'email.faltaProfesor', $elemento);
                Alert::info("Correos enviados a $item");
            }
        }
    }

}