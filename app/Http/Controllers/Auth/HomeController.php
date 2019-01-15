<?php

namespace Intranet\Http\Controllers\Auth;

use Intranet\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Intranet\Entities\Menu;
use Intranet\Entities\Profesor;
use Intranet\Entities\Alumno;
use Intranet\Entities\Actividad;
use Intranet\Entities\Activity;
use Intranet\Entities\Falta;
use Intranet\Entities\Horario;
use Intranet\Entities\Falta_profesor;
use Intranet\Entities\AlumnoGrupo;
use Intranet\Entities\Documento;
use Illuminate\Support\Facades\Session;

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
            $usuario = Profesor::findOrFail(AuthUser()->dni);
            if ($usuario->dni == '12345678A')
                return redirect('/fichar');
            else {
                $horario = Horario::HorarioSemanal($usuario->dni);
                $actividades = Actividad::next()->auth()->orderby('desde','asc')->take(10)->get();
                $activities = Activity::Profesor($usuario->dni)
                                ->orderBy('updated_at', 'desc')
                                ->take(15)->get();
                $documents = Documento::where('curso', '=', Curso())->where('tipoDocumento', '=', 'Acta')
                                ->where('grupo', '=', 'Claustro')->orWhere('grupo', '=', 'COCOPE')->get();
                $faltas = Falta::Dia(Hoy())->get();

                if (!estaDentro() && !Session::get('userChange')) {
                    Falta_profesor::fichar($usuario->dni);
                }
                return view('home.profile', compact('usuario', 'horario', 'actividades', 'activities', 'documents','faltas'));
            }
        } else {
            $usuario = Alumno::findOrFail(AuthUser()->nia);
            if (isset(AlumnoGrupo::where('idAlumno', AuthUser()->nia)->first()->idGrupo)) {
                $grupo = AlumnoGrupo::where('idAlumno', AuthUser()->nia)->first()->idGrupo;
                $horario = Horario::HorarioGrupo($grupo);
            }
            $actividades = Actividad::next()->auth()->take(10)->get();
            $activities = Activity::Profesor($usuario->dni)
                            ->orderBy('updated_at', 'desc')
                            ->take(15)->get();
            $documents = Documento::where('curso', '=', Curso())->where('tipoDocumento', '=', 'Alumno')->get();

            return view('home.alumno', compact('usuario', 'horario', 'actividades', 'activities', 'documents'));
        }
    }

    public function legal()
    {
        return view('intranet.legal');
    }

}
