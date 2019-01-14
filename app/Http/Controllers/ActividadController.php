<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Intranet\Entities\Actividad;
use Intranet\Entities\Grupo;
use Intranet\Entities\ActividadGrupo;
use Intranet\Entities\Actividad_profesor;
use Intranet\Entities\Profesor;
use Intranet\Entities\Alumno;
use Intranet\Entities\Resultado;
use Response;
use Exception;
use Illuminate\Support\Facades\Redirect;
use Intranet\Botones\BotonBasico;
use Intranet\Botones\BotonIcon;
use Intranet\Botones\BotonImg;
use Styde\Html\Facades\Alert;
use Jenssegers\Date\Date;
use Illuminate\Support\Facades\Session;
use DB;

class ActividadController extends IntranetController
{

    use traitAutorizar,    
        traitImprimir,
        traitNotificar;

    protected $perfil = 'profesor';
    protected $model = 'Actividad';
    protected $gridFields = ['name', 'desde', 'hasta', 'situacion'];
    protected $modal = true;
    
    protected function search()
    {
        return Actividad::Profesor(AuthUser()->dni)
                ->where('extraescolar', 1)
                ->get();
    }
    
    

    public function store(Request $request)
    {
        $id = $this->realStore($request);
        return redirect()->route('actividad.detalle', ['actividad' => $id]);
    }

    public function detalle($id)
    {
        $tGrupos = Grupo::pluck('nombre', 'codigo')->toArray();
        $Actividad = Actividad::find($id);
        $Profesores = Profesor::select('apellido1', 'apellido2', 'nombre', 'dni')
                ->Activo()
                ->OrderBy('apellido1')
                ->OrderBy('apellido2')
                ->get();
        foreach ($Profesores as $profesor) {
            $tProfesores[$profesor->dni] = $profesor->apellido1 . ' ' . $profesor->apellido2 . ',' . $profesor->nombre;
        }
        $sProfesores = $Actividad->profesores()
                ->orderBy('apellido1')
                ->get(['dni', 'apellido1', 'apellido2', 'nombre', 'coordinador']);
        $sGrupos = $Actividad->grupos()->get(['codigo', 'nombre']);
        return view('extraescolares.edit', compact('Actividad', 'tProfesores', 'tGrupos', 'sGrupos', 'sProfesores'));
    }

    public function altaGrupo(Request $request, $actividad_id)
    {
        $actividad = Actividad::find($actividad_id);
        $actividad->grupos()->syncWithoutDetaching([$request->idGrupo]);
        return redirect()->route('actividad.detalle', ['actividad' => $actividad_id]);
    }

    public function borrarGrupo($actividad_id, $grupo_id)
    {
        $actividad = Actividad::find($actividad_id);
        $actividad->grupos()->detach($grupo_id);
        return redirect()->route('actividad.detalle', ['actividad' => $actividad_id]);
    }

    public function altaProfesor(Request $request, $actividad_id)
    {
        $actividad = Actividad::find($actividad_id);
        $actividad->profesores()->syncWithoutDetaching([$request->idProfesor]);
        return redirect()->route('actividad.detalle', ['actividad' => $actividad_id]);
    }

    public function borrarProfesor($actividad_id, $profesor_id)
    {

        $actividad = Actividad::find($actividad_id);
        if ($actividad->profesores()->count() == 1) {
            Alert::info('No es pot donar de baixa el últim profesor');
            return back();
        }
        $actividad->profesores()->detach($profesor_id);
        if ((!Actividad_profesor::where('idActividad', '=', $actividad_id)
                        ->where('coordinador', '=', '1')
                        ->count())) {
            $nuevo_coord = Actividad_profesor::where('idActividad', '=', $actividad_id)
                    ->where('coordinador', '=', '0')
                    ->first();
            $actividad->profesores()->updateExistingPivot($nuevo_coord->idProfesor, ['coordinador' => 1]);
        }
        return redirect()->route('actividad.detalle', ['actividad' => $actividad_id]);
    }

    public function coordinador($actividad_id, $profesor_id)
    {
        $actividad = Actividad::find($actividad_id);
        $coordActual = Actividad_profesor::where('idActividad', '=', $actividad_id)
                ->where('coordinador', '=', '1')
                ->first();
        if ($coordActual)
            $actividad->profesores()->updateExistingPivot($coordActual->idProfesor, ['coordinador' => 0]);
        $actividad->profesores()->updateExistingPivot($profesor_id, ['coordinador' => 1]);
        return redirect()->route('actividad.detalle', ['actividad' => $actividad_id]);
    }

    public function notify($id)
    {
        $elemento = Actividad::findOrFail($id);
        $profesores = Actividad_profesor::where('idActividad', '=', $id)->get();
        $grupos = ActividadGrupo::where('idActividad', '=', $id)->get();
        $mensaje = "Els grups: -";
        foreach ($grupos as $grup) {
            $grupo = Grupo::where('codigo', '=', $grup->idGrupo)
                    ->first();
            $mensaje .= $grupo->nombre . "- ";
        }
        $mensaje .= "se'n van a l'activitat extraescolar: " . $elemento->name . " i jo me'n vaig amb ells. ";
        $mensaje .= 'Estarem fora des de ' . $elemento->desde . " fins " . $elemento->hasta;
        foreach ($profesores as $profe) {
            $profesor = Profesor::where('dni', $profe->idProfesor)->first();
            $this->avisaProfe($elemento, $profesor->dni, $mensaje, $profesor->nombre . " " . $profesor->apellido1);
        }
        return back();
    }

    public function autorizacion($id)
    {
        $grups = [];
        $elemento = Actividad::findOrFail($id);
        $grupos = ActividadGrupo::select('idGrupo')->where('idActividad', '=', $id)->get();
        foreach ($grupos as $grupo) {
            $grups[] = $grupo->idGrupo;
        }
        $todos = Alumno::join('alumnos_grupos', 'idAlumno', '=', 'nia')
                ->select('alumnos.*', 'idGrupo')
                ->QGrupo($grups)
                ->Menor($elemento->desde)
                ->orderBy('idGrupo')
                ->orderBy('apellido1')
                ->orderBy('apellido2')
                ->get();
        return $this->hazPdf('pdf.autorizacionMenores', $todos, $elemento, 'portrait')->stream();
    }

    protected function iniBotones()
    {

        $this->panel->setBotonera(['create']);
        $this->panel->setBothBoton('actividad.detalle', ['where' => ['estado', '<', '2']]);
        $this->panel->setBothBoton('actividad.edit', ['where' => ['estado', '<', '2']]);
        $this->panel->setBothBoton('actividad.init', ['where' => ['estado', '==', '0']]);
        $this->panel->setBothBoton('actividad.notification', ['where' => ['estado', '>', '0', 'estado', '<', '3', 'coord', '==', '1']]);
        $this->panel->setBothBoton('actividad.autorizacion', ['where' => ['estado', '>', '0']]);
        $this->panel->setBoton('grid', new BotonImg('actividad.delete', ['where' => ['estado', '<', '2']]));
        $this->panel->setBoton('profile', new BotonIcon('actividad.delete', ['class' => 'btn-danger', 'where' => ['estado', '<', '2']]));
        $this->panel->setBoton('grid', new BotonImg('actividad.ics', ['img' => 'fa-calendar', 'where' => ['desde', 'posterior', Date::yesterday()]]));
    }

    public function autorizar()
    {
        $this->makeAll(Actividad::where('estado', '1')->get(), 2);
        return back();
    }


    public function i_c_s($id)
    {
        return $this->ics($id, 'name', 'descripcion');
    }

}
