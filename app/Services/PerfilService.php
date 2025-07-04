<?php

namespace Intranet\Services;

use Intranet\Entities\Actividad;
use Intranet\Entities\AlumnoGrupo;
use Intranet\Entities\Horario;
use Intranet\Entities\Task;
use Intranet\Entities\Reunion;
use Intranet\Entities\Falta;
use Intranet\Entities\Comision;
use Illuminate\Support\Facades\Cache;


class PerfilService
{
    public function carregarDadesProfessor(string $dni): array
    {
        $horari = Cache::remember("horario$dni", now()->addDay(), fn () =>
        Horario::HorarioSemanal($dni)
        );

        $activitats = Cache::remember('actividades', now()->addHour(), fn () =>
        Actividad::next()
            ->with(['profesores', 'grupos', 'Tutor'])
            ->auth()
            ->orderBy('desde', 'asc')
            ->take(10)
            ->get()
        );

        $tasques = Task::misTareas()->orderBy('vencimiento')->get();

        $reunions = Reunion::with('profesores')->next()->orderBy('fecha')->get();

        $faltas = Falta::select('idProfesor', 'dia_completo', 'hora_ini', 'hora_fin')
            ->with('profesor')
            ->Dia(Hoy())
            ->get();

        $activitatsHui = Actividad::Dia(Hoy())
            ->where('estado', '>', 1)
            ->where('fueraCentro', '=', 1)
            ->get();

        $comissions = Cache::remember('comisionesHui', now()->addHours(6), fn () =>
        Comision::with('profesor')->Dia(Hoy())->get()
        );

        return compact('horari', 'activitats', 'tasques', 'reunions', 'faltas', 'activitatsHui', 'comissions');
    }

    public function carregarDadesAlumne(string $nia): array
    {
        $grupo = AlumnoGrupo::where('idAlumno', $nia)->first()?->idGrupo;
        $horari = $grupo ? Horario::HorarioGrupo($grupo) : [];

        $activitats = Actividad::next()->auth()->take(10)->get();
        $documents = Documento::where('curso', '=', Curso())
            ->where('tipoDocumento', '=', 'Alumno')
            ->get();

        return compact('grupo', 'horari', 'activitats', 'documents');
    }
}