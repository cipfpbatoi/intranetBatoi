<?php

namespace Intranet\Services\Auth;

use Intranet\Application\Comision\ComisionService;
use Intranet\Application\Horario\HorarioService;
use Intranet\Entities\Actividad;
use Intranet\Entities\AlumnoGrupo;
use Intranet\Entities\Documento;
use Intranet\Entities\Task;
use Intranet\Entities\Reunion;
use Intranet\Entities\Falta;
use Illuminate\Support\Facades\Cache;


class PerfilService
{
    private ComisionService $comisionService;
    private HorarioService $horarioService;

    public function __construct(ComisionService $comisionService, HorarioService $horarioService)
    {
        $this->comisionService = $comisionService;
        $this->horarioService = $horarioService;
    }

    public function carregarDadesProfessor(string $dni): array
    {

        $horario = Cache::remember("horario$dni", now()->addDay(), fn () =>
        $this->horarioService->semanalByProfesor($dni)
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
        $this->comisionService->withProfesorByDay(Hoy())
        );

        return compact('horario', 'actividades', 'tasks', 'reuniones', 'faltas', 'hoyActividades', 'comisiones');
    }

    public function carregarDadesAlumne(string $nia): array
    {
        $grupo = AlumnoGrupo::where('idAlumno', $nia)->first()?->idGrupo;
        $horario = $grupo ? $this->horarioService->semanalByGrupo((string) $grupo) : [];

        $actividades = Actividad::next()->auth()->take(10)->get();
        $activities = [];
        $documents = Documento::where('curso', '=', Curso())
            ->where('tipoDocumento', '=', 'Alumno')
            ->get();

        return compact( 'grupo', 'horario', 'actividades','activities' ,'documents');
    }
}
