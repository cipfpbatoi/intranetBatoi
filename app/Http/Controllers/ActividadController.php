<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Intranet\Componentes\Mensaje;
use Intranet\Componentes\Pdf as PDF;
use Intranet\Entities\Actividad;
use Intranet\Entities\Grupo;
use Intranet\Entities\ActividadGrupo;
use Intranet\Entities\ActividadProfesor;
use Intranet\Entities\Profesor;
use Intranet\Entities\Alumno;
use Intranet\Services\CalendarService;
use Intranet\Services\GestorService;
use Response;
use Intranet\Botones\BotonIcon;
use Intranet\Botones\BotonImg;
use Styde\Html\Facades\Alert;
use Jenssegers\Date\Date;
use DB;
use Intranet\Http\Requests\ActividadRequest;
use Intranet\Http\Requests\ValoracionRequest;
use Intranet\Services\AdviseTeacher;


class ActividadController extends ModalController
{

    use traitAutorizar,  traitSCRUD;

    protected $perfil = 'profesor';
    protected $model = 'Actividad';
    protected $gridFields = ['name', 'desde', 'hasta', 'situacion'];
    protected $formFields= [
        'id' => ['type' => 'hidden'],
        'name' => ['type' => 'text'],
        'desde' => ['type' => 'datetime'],
        'hasta' => ['type' => 'datetime'],
        'poll' => ['type' => 'hidden'],
        'fueraCentro' => ['type' => 'checkbox'],
        'transport' => ['type' => 'checkbox'],
        'descripcion' => ['type' => 'textarea'],
        'objetivos' => ['type' => 'textarea'],
        'extraescolar' => ['type' => 'hidden'],
        'comentarios' => ['type' => 'textarea'],
        'recomanada' => ['type' => 'hidden']
    ];
    
    protected function search()
    {
        return Actividad::Profesor(AuthUser()->dni)
                ->where('extraescolar', 1)
                ->get();
    }


    protected function createWithDefaultValues( $default=[])
    {
        $data = new Date('tomorrow');
        return new Actividad(['extraescolar' => 1,'desde'=>$data,'hasta'=>$data,'poll' => 0,'recomanada'=>1]);
    }

    public function store(ActividadRequest $request)
    {
        $new = new Actividad();
        $new->fillAll($request);
        return $this->showDetalle($new);
    }

    public function update(ActividadRequest $request, $id)
    {
        Actividad::findOrFail($id)->fillAll($request);
        return $this->redirect();
    }

    public function valoracion(ValoracionRequest $request)
    {
        $actividad = Actividad::findOrFail($request->idActividad);
        $actividad->desenvolupament = $request->desenvolupament;
        $actividad->valoracio = $request->valoracio;
        $actividad->dades = $request->dades;
        $actividad->aspectes = $request->aspectes;
        $actividad->recomanada = isset($request->recomanada)?1:0;
        $actividad->estado = 4;

        $actividad->save();
        return back();
    }


    public function showValue($id){
        $Actividad = Actividad::find($id);
        return view('extraescolares.showValue', compact('Actividad'));
    }

    public function value($id){
        $Actividad = Actividad::find($id);
        return view('extraescolares.value', compact('Actividad'));
    }

    public function printValue($id){
        $elemento = $this->class::findOrFail($id);
        $informe = 'pdf.valoracionActividad';
        $pdf = PDF::hazPdf($informe, $elemento, null);
        return $pdf->stream();
    }

    private function showDetalle($id){
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
        return $this->showDetalle($actividad_id);
    }

    public function borrarGrupo($actividad_id, $grupo_id)
    {
        $actividad = Actividad::find($actividad_id);
        $actividad->grupos()->detach($grupo_id);
        return $this->showDetalle($actividad_id);
    }

    public function altaProfesor(Request $request, $actividad_id)
    {
        $actividad = Actividad::find($actividad_id);
        $actividad->profesores()->syncWithoutDetaching([$request->idProfesor]);
        return $this->showDetalle($actividad_id);
    }

    public function borrarProfesor($actividad_id, $profesor_id)
    {
        $actividad = Actividad::find($actividad_id);
        if ($actividad->profesores()->count() == 1) {
            Alert::info('No es pot donar de baixa el últim profesor');
            return back();
        }
        $actividad->profesores()->detach($profesor_id);
        if (!ActividadProfesor::where('idActividad', '=', $actividad_id)
                        ->where('coordinador', '=', '1')
                        ->count()) {
            $nuevo_coord = ActividadProfesor::where('idActividad', '=', $actividad_id)
                    ->where('coordinador', '=', '0')
                    ->first();
            $actividad->profesores()->updateExistingPivot($nuevo_coord->idProfesor, ['coordinador' => 1]);
        }
        return $this->showDetalle($actividad_id);
    }

    public function coordinador($actividad_id, $profesor_id)
    {
        $actividad = Actividad::find($actividad_id);
        $coordActual = ActividadProfesor::where('idActividad', '=', $actividad_id)
                ->where('coordinador', '=', '1')
                ->first();
        if ($coordActual){
            $actividad->profesores()->updateExistingPivot($coordActual->idProfesor, ['coordinador' => 0]);
        }
        $actividad->profesores()->updateExistingPivot($profesor_id, ['coordinador' => 1]);
        return $this->showDetalle($actividad_id);
    }

    public function notify($id)
    {
        $elemento = Actividad::findOrFail($id);
        $coordinador = Profesor::find($elemento->Creador());
        foreach ($elemento->grupos as $grupo){
            $mensaje = $this->hazMensajeGrupo($elemento,$grupo);
            foreach (Profesor::Grupo($grupo->codigo)->get() as $profesor){
                Mensaje::send($profesor->dni, $mensaje, '#', $coordinador->shortName);
            }
        }

        $mensaje = $this->hazMensaje($elemento);
        foreach ($elemento->profesores as $profesor) {
            AdviseTeacher::exec($elemento,  $mensaje, $profesor->dni,$profesor->shortName);
        }
        return back();
    }

    protected function hazMensajeGrupo($elemento,$grupo){
        $mensaje = "El grup {$grupo->nombre} se'n van a l'activitat extraescolar:  {$elemento->name}.";
        return $mensaje . 'Estarà fora des de ' . $elemento->desde . " fins " . $elemento->hasta;
    }

    protected function hazMensaje($elemento){
        
        $mensaje = "Els grups: -";
        foreach ($elemento->grupos as $grupo) {
            $mensaje .= $grupo->nombre . "- ";
        }
        $mensaje .= "se'n van a l'activitat extraescolar: " . $elemento->name . " i jo me'n vaig amb ells. ";
        return $mensaje . 'Estarem fora des de ' . $elemento->desde . " fins " . $elemento->hasta;
    }
    
    public function autorizacion($id)
    {
        $grups = [];
        $actividad = Actividad::findOrFail($id);
        $grups = hazArray(ActividadGrupo::select('idGrupo')->where('idActividad', '=', $id)->get(),'idGrupo');
        $todos = Alumno::join('alumnos_grupos', 'idAlumno', '=', 'nia')
                ->select('alumnos.*', 'idGrupo')
                ->QGrupo($grups)
                ->Menor($actividad->desde)
                ->orderBy('idGrupo')
                ->orderBy('apellido1')
                ->orderBy('apellido2')
                ->get();
        if ($actividad->menores()->count()==0){
            foreach ($todos as $alumno){
                $actividad->menores()->attach($alumno->nia,['autorizado' => 0]);
            }
        }
        if ($todos->count()){
            $pdf = PDF::hazPdf('pdf.autoritzacioMenors', $todos, $actividad, 'portrait');
            return $pdf->stream();
        }
        Alert::info('No hi han menors');
        return back();
    }

    public function autorize($id){
        $actividad = Actividad::findOrFail($id);
        if ($actividad->menores()->count()) {
            return view('extraescolares.autorizados', compact('actividad'));
        }
        Alert::danger('No has imprés cap autorització');
        return back();
    }

    protected function iniBotones()
    {
        $this->panel->setBotonera(['create']);
        $this->panel->setBothBoton('actividad.detalle', ['where' => ['estado', '<', '2']]);
        $this->panel->setBothBoton('actividad.edit', ['where' => ['estado', '<', '2']]);
        $this->panel->setBothBoton('actividad.init', ['where' => ['estado', '==', '0']]);
        $this->panel->setBothBoton('actividad.notification', ['where' => ['estado', '>', '0', 'estado', '<', '4', 'coord', '==', '1','desde','posterior',Hoy()]]);
        $this->panel->setBothBoton('actividad.autorizacion', ['where' => ['estado', '>', '0','estado','<','4','desde','posterior',Hoy()]]);
        $this->panel->setBoton('grid',new BotonImg('actividad.pdfVal', ['img'=>'fa-file-pdf-o','where' => ['estado', '==', '4','hasta','anterior',Hoy()]]));
        $this->panel->setBoton('grid',new BotonImg('actividad.showVal', ['img'=>'fa-eye-slash','where' => ['estado', '==', '4','hasta','anterior',Hoy()]]));
        $this->panel->setBoton('grid',new BotonImg('actividad.autorize', ['img'=>'fa-filter','where' => ['estado', '>', '0','estado','<=','3','desde','posterior',Hoy()]]));
        $this->panel->setBoton('grid',new BotonImg('actividad.value', ['img'=>'fa-eyedropper','where' => ['estado', '>=', '3','hasta','anterior',Hoy()]]));
        $this->panel->setBoton('grid', new BotonImg('actividad.delete', ['where' => ['estado', '<', '2']]));
        $this->panel->setBoton('profile', new BotonIcon('actividad.delete', ['class' => 'btn-danger', 'where' => ['estado', '<', '2']]));
        $this->panel->setBoton('grid', new BotonImg('actividad.ics', ['img' => 'fa-calendar', 'where' => ['desde', 'posterior', Date::yesterday()]]));
    }

    public function autorizar()
    {
        $this->makeAll(Actividad::where('estado', '1')->get(), 2);
        return back();
    }

    public function printAutoritzats(){
        return $this->imprimir('extraescolars');
    }


    public function i_c_s($id)
    {
        $elemento = $this->class::findOrFail($id);
        $vCalendar = CalendarService::build($elemento,'name','descripcion');
        return Response::view('ics', compact('vCalendar'))->header('Content-Type', 'text/calendar');

    }

    public function menorAuth($nia,$id){
        $actividad = Actividad::findOrFail($id);
        if ($actividad->menores()->where('nia',$nia)->first()->pivot->autorizado){
            $autorizado = 0;
        } else {
            $autorizado = 1;
        }
        $actividad->menores()->updateExistingPivot($nia,['autorizado' => $autorizado]);
        return view('extraescolares.autorizados',compact('actividad'));
    }

    public function gestor($id)
    {
        $gestor = new GestorService(Actividad::findOrFail($id));
        return $gestor->render();
    }
}
