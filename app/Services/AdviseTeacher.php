<?php
namespace Intranet\Services;

use Intranet\Componentes\Mensaje;
use Intranet\Entities\Grupo;
use Intranet\Entities\Hora;
use Intranet\Entities\Horario;
use Intranet\Entities\Profesor;
use Intranet\Jobs\SendEmail;
use Styde\Html\Facades\Alert;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use function hora, nameDay;

class AdviseTeacher
{
    public static function exec(object $elemento, ?string $mensaje = null, ?string $idEmisor = null, ?string $emisor = null): void
    {
        $mensaje = $mensaje ?? "No estaré en el centre des de " . $elemento->desde . " fins " . $elemento->hasta;
        $idEmisor = $idEmisor ?? $elemento->idProfesor;
        $emisor = $emisor ?? Profesor::find($idEmisor) ?? 'Sistema';

        $grupos = self::gruposAfectados($elemento, $idEmisor);
        if ($grupos->isEmpty()) {
            return;
        }

        foreach (self::profesoresAfectados($grupos, $idEmisor) as $profesor) {
            try {
                Mensaje::send($profesor->idProfesor, $mensaje, '#', $emisor  );
            } catch (\Exception $e) {
                Alert::danger("Error al enviar mensaje a {$profesor->idProfesor}");
            }
        }
    }

    private static function profesoresAfectados(Collection $grupos, string  $emisor): Collection
    {
        return Horario::distinct()
            ->select('idProfesor')
            ->whereIn('idGrupo', $grupos->pluck('idGrupo'))
            ->where('idProfesor', '<>', $emisor)
            ->get();
    }

    public static function gruposAfectados(object $elemento, string $idProfesor): Collection
    {
        if (!Carbon::parse($elemento->desde)->isSameDay(Carbon::parse($elemento->hasta))) {
            return Horario::distinct()
                ->select('idGrupo')
                ->Profesor($idProfesor)
                ->whereNotNull('idGrupo')
                ->get();
        }

        $diaSemana = nameDay($elemento->desde);
        $horas = self::horasAfectadas($elemento);

        if ($horas->isNotEmpty()) {
            return Horario::distinct()
                ->select('idGrupo')
                ->Profesor($idProfesor)
                ->Dia($diaSemana)
                ->whereNotNull('idGrupo')
                ->whereIn('sesion_orden', $horas)
                ->get();
        }

        return collect();
    }

    private static function horasAfectadas(object $elemento): Collection
    {
        if (!isset($elemento->dia_completo)) {
            return Hora::horasAfectadas(hora($elemento->desde), hora($elemento->hasta));
        }

        return $elemento->dia_completo
            ? Hora::horasAfectadas('07:00', '23:00')
            : Hora::horasAfectadas($elemento->hora_ini, $elemento->hora_fin);
    }

    public static function sendEmailTutor(object $elemento): void
    {
        $idEmisor = $elemento->idProfesor;
        foreach (self::gruposAfectados($elemento, $idEmisor) as $grupoItem) {
            $grupo = Grupo::find($grupoItem->idGrupo);

            if ($grupo && $grupo->Tutor) {
                $correoTutor = $grupo->Tutor->Sustituye->email ?? $grupo->Tutor->email;
                $remitente = ['nombre' => 'Caporalia', 'email' => 'intranet@cipfpbatoi.es'];

                SendEmail::dispatch($correoTutor, $remitente, 'email.faltaProfesor', $elemento);
                Alert::info("Correu enviat al tutor del grup {$grupo->idGrupo}");
            } else {
                Alert::info("No hi ha tutor per al grup {$grupoItem->idGrupo}");
            }
        }
    }

    public static function horariAltreGrup(object $elemento, string $professorId): Collection
    {
        $horas = self::gruposAfectados($elemento, $professorId);
        $grupos = collect($elemento->grupos)->pluck('codigo');

        return $horas->whereNotIn('idGrupo', $grupos)->values();
    }
}
