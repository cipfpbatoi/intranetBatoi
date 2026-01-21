<?php

namespace Intranet\Services;

use Intranet\Entities\Actividad;
use Intranet\Entities\AlumnoGrupo;
use Intranet\Entities\Documento;
use Intranet\Entities\Horario;
use Intranet\Entities\Task;
use Intranet\Entities\Reunion;
use Intranet\Entities\Falta;
use Intranet\Entities\Comision;
use Illuminate\Support\Facades\Cache;


/**
 * Servei PerfilService.
 */
class PerfilService
{
    public function carregarDadesProfessor(string $dni): array
    {

        $horario = Cache::remember("horario$dni", now()->addDay(), fn () =>
        Horario::HorarioSemanal($dni)
        );

        $actividades = Cache::remember('actividades', now()->addHour(), fn () =>
        Actividad::next()
            ->with(['profesores', 'grupos', 'Tutor'])
            ->auth()
            ->orderBy('desde', 'asc')
            ->take(10)
            ->get()
        );

        $tasks = Task::misTareas()->orderBy('vencimiento')->get();

        $reuniones = Reunion::with('profesores')->next()->orderBy('fecha')->get();

        $faltas = Falta::select('idProfesor', 'dia_completo', 'hora_ini', 'hora_fin')
            ->with('profesor')
            ->Dia(Hoy())
            ->get();

        $hoyActividades = Actividad::Dia(Hoy())
            ->where('estado', '>', 1)
            ->where('fueraCentro', '=', 1)
            ->get();

        $comisiones = Cache::remember('comisionesHui', now()->addHours(6), fn () =>
        Comision::with('profesor')->Dia(Hoy())->get()
        );

        return compact('horario', 'actividades', 'tasks', 'reuniones', 'faltas', 'hoyActividades', 'comisiones');
    }

    public function carregarDadesAlumne(string $nia): array
    {
        $grupo = AlumnoGrupo::where('idAlumno', $nia)->first()?->idGrupo;
        $horario = $grupo ? Horario::HorarioGrupo($grupo) : [];

        $actividades = Actividad::next()->auth()->take(10)->get();
        $activities = [];
        $documents = Documento::where('curso', '=', Curso())
            ->where('tipoDocumento', '=', 'Alumno')
            ->get();

        return compact( 'grupo', 'horario', 'actividades','activities' ,'documents');
    }
}