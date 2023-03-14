<?php

namespace Intranet\Http\Controllers\Auth;

use Intranet\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Intranet\Entities\Actividad;
use Intranet\Entities\Reunion;
use Intranet\Entities\Falta;
use Intranet\Entities\Horario;
use Intranet\Entities\Falta_profesor;
use Intranet\Entities\AlumnoGrupo;
use Intranet\Entities\Documento;
use Illuminate\Support\Facades\Session;
use Intranet\Entities\Comision;
use Intranet\Entities\Task;

/**
 * Description of HomeIdentifyController
 *
 * @author igomis
 */
abstract class HomeController extends Controller
{

    protected $guard;

    public function __construct()
    {
        $this->middleware($this->guard);
        
    }

    public function index()
    {
        if ($this->guard == 'profesor') {
            $usuario = AuthUser();
            if ($usuario->dni == '12345678A') {
                return redirect('/fichar');
            } else {
                $horario = Cache::remember('horario'.$usuario->dni, now()->addDay(), function () use ($usuario) {
                    return Horario::HorarioSemanal($usuario->dni);
                });
                $actividades =  Cache::remember('actividades', now()->addHour(), function () {
                    return Actividad::next()
                        ->with(['profesores','grupos','Tutor'])
                        ->auth()
                        ->orderby('desde', 'asc')
                        ->take(10)
                        ->get();
                });
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
                $comisiones = Cache::remember('comisionesHui', now()->addHours(6), function () {
                   return(Comision::with('profesor')->Dia(Hoy())->get());
                });

                if (!estaDentro() && !Session::get('userChange')) {
                    Falta_profesor::fichar($usuario->dni);
                }
                return view(
                    'home.profile',
                    compact(
                        'usuario',
                        'horario',
                        'actividades',
                        'tasks',
                        'reuniones',
                        'faltas',
                        'hoyActividades',
                        'comisiones'
                    )
                );
            }
        } else {
            $usuario = AuthUser();
            if (isset(AlumnoGrupo::where('idAlumno', AuthUser()->nia)->first()->idGrupo)) {
                $grupo = AlumnoGrupo::where('idAlumno', AuthUser()->nia)->first()->idGrupo;
                $horario = Horario::HorarioGrupo($grupo);
            } else {
                $grupo = [];
                $horario = [];
            }
            $actividades = Actividad::next()->auth()->take(10)->get();
            $activities = [];
            $documents = Documento::where('curso', '=', Curso())->where('tipoDocumento', '=', 'Alumno')->get();

            return view('home.alumno', compact('usuario', 'horario', 'actividades', 'activities', 'documents'));
        }
    }

    public function legal()
    {
        return view('intranet.legal');
    }

}
