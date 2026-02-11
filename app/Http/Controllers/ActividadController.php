<?php

namespace Intranet\Http\Controllers;

use Intranet\Http\Controllers\Core\ModalController;

use DB;
use Illuminate\Http\Request;
use Intranet\UI\Botones\BotonIcon;
use Intranet\UI\Botones\BotonImg;
use Intranet\Services\Notifications\NotificationService;
use Intranet\Services\Document\PdfService;
use Intranet\Entities\Actividad;
use Intranet\Entities\ActividadGrupo;
use Intranet\Entities\ActividadProfesor;
use Intranet\Entities\Alumno;
use Intranet\Entities\Grupo;
use Intranet\Entities\Profesor;
use Intranet\Http\Requests\ActividadRequest;
use Intranet\Http\Requests\ValoracionRequest;
use Intranet\Http\Traits\Autorizacion;
use Intranet\Http\Traits\Core\SCRUD;
use Intranet\Services\Notifications\AdviseTeacher;
use Intranet\Services\Calendar\CalendarService;
use Intranet\Services\General\GestorService;
use Intranet\Services\Calendar\GoogleCalendarService;
use Intranet\Services\General\StateService;
use Jenssegers\Date\Date;
use Illuminate\Support\Facades\Response;
use Styde\Html\Facades\Alert;


class ActividadController extends ModalController
{

    use Autorizacion, SCRUD;

    protected $perfil = 'profesor';
    protected $model = 'Actividad';
    protected $gridFields = ['name', 'desde', 'hasta', 'situacion'];
    protected $formFields= [
        'id' => ['type' => 'hidden'],
        'tipo_actividad_id' => ['type' => 'select'],
        'name' => ['type' => 'text'],
        'desde' => ['type' => 'datetime'],
        'hasta' => ['type' => 'datetime'],
        'poll' => ['type' => 'hidden'],
        'complementaria' => ['type' => 'checkbox'],
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
        return new Actividad(['extraescolar' => 1,'desde'=>$data,'hasta'=>$data,'poll' => 0,'recomanada'=>1,'complementaria'=>1,'fueraCentro'=>0,'transport'=>0]);
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
        return app(PdfService::class)->hazPdf($informe, $elemento, null)->stream();
    }

    private function showDetalle($id){
        return redirect()->route('actividad.detalle', ['actividad' => $id]);
    }

    public function detalle($id)
    {
        $Actividad = Actividad::with(['profesores' => function ($query) {
            $query->select('dni', 'apellido1', 'apellido2', 'nombre', 'coordinador')
                ->orderBy('apellido1')
                ->orderBy('apellido2');
        }, 'grupos:codigo,nombre'])->findOrFail($id);

        // Obtenir tots els professors actius i estructurar-los en un array associatiu
        $tProfesores = Profesor::Activo()
            ->orderBy('apellido1')
            ->orderBy('apellido2')
            ->get()
            ->mapWithKeys(fn($p) => [$p->dni => "$p->apellido1 $p->apellido2, $p->nombre"])
            ->toArray();

        // Llista de tots els grups disponibles
        $tGrupos = Grupo::pluck('nombre', 'codigo')->toArray();

        // Assignem professors i grups associats a l'activitat
        $sProfesores = $Actividad->profesores;
        $sGrupos = $Actividad->grupos;

        return view('extraescolares.edit', compact('Actividad', 'tProfesores', 'tGrupos', 'sGrupos', 'sProfesores'));
    }


    public function altaGrupo(Request $request, $actividad_id)
    {
        $actividad = Actividad::find($actividad_id);
        $actividad->grupos()->syncWithoutDetaching([$request->idGrupo]);
        return $this->showDetalle($actividad_id);
    }

    private function desassignar($actividad_id, $relation, $id)
    {
        $actividad = Actividad::findOrFail($actividad_id);
        $actividad->$relation()->detach($id);


    }

    public function borrarGrupo($actividad_id, $grupo_id)
    {
        $this->desassignar($actividad_id, 'grupos', $grupo_id);
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
        $this->desassignar($actividad_id, 'profesores', $profesor_id);

        $nuevo_coord = $actividad->profesores()->wherePivot('coordinador', 0)->first();
        if ($nuevo_coord) {
            $actividad->profesores()->updateExistingPivot($nuevo_coord->dni, ['coordinador' => 1]);
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
        $actividad = Actividad::findOrFail($id);
        $coordinador = Profesor::find($actividad->Creador());

        $this->notificarGrupos($actividad, $coordinador);
        $this->notificarProfessors($actividad);

        return back();
    }

    private function notificarGrupos($actividad, $coordinador)
    {
        foreach ($actividad->grupos as $grupo) {
            $mensaje = "El grup {$grupo->nombre} se’n va a l’activitat {$actividad->name}.";
            foreach (Profesor::Grupo($grupo->codigo)->get() as $profesor) {
                app(NotificationService::class)->send($profesor->dni, $mensaje, '#', $coordinador->shortName);
            }
        }
    }

    private function notificarProfessors($actividad)
    {
        $mensaje = "Els grups: " . $actividad->grupos->implode('nombre', ', ') .
            " van a l’activitat {$actividad->name} i jo me’n vaig amb ells. " .
            "Estarem fora des de {$actividad->desde} fins {$actividad->hasta}.";

        foreach ($actividad->profesores as $profesor) {
            AdviseTeacher::exec($actividad, $mensaje, $profesor->dni, $profesor->shortName);
        }
    }

    public function autorizacion($id)
    {
        $grups = [];
        $actividad = Actividad::findOrFail($id);
        $grups = ActividadGrupo::where('idActividad', $id)->pluck('idGrupo')->toArray();
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
            $pdf = app(PdfService::class)->hazPdf('pdf.autoritzacioMenors', $todos, $actividad, 'portrait');
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
        $this->panel->setBothBoton('actividad.detalle', ['where' => ['estado', '<', '5']]);
        $this->panel->setBothBoton('actividad.edit', ['where' => ['estado', '<', '2']]);
        $this->panel->setBothBoton('actividad.init', ['where' => ['estado', '==', '0']]);
        $this->panel->setBothBoton('actividad.notification', ['where' => ['estado', '>', '0', 'estado', '<', '4', 'coord', '==', '1','desde','posterior',Hoy()]]);
        $this->panel->setBothBoton('actividad.autorizacion', ['where' => ['estado', '>', '0','estado','<','4','desde','posterior',Hoy()]]);
        $this->panel->setBoton('grid',new BotonImg('actividad.pdfVal', ['img'=>'fa-file-pdf-o','where' => ['estado', '==', '4','hasta','anterior',Hoy()]]));
        $this->panel->setBoton('grid',new BotonImg('actividad.showVal', ['img'=>'fa-eye-slash','where' => ['estado', '==', '4','hasta','anterior',Hoy()]]));
        $this->panel->setBoton('grid',new BotonImg('actividad.autorize', ['img'=>'fa-filter','where' => ['estado', '>', '0','estado','<=','3','desde','posterior',Hoy()]]));
        $this->panel->setBoton('grid',new BotonImg('actividad.value', ['img'=>'fa-eyedropper','where' => ['estado', '>=', '3','hasta','anterior',Hoy(),'coord','==',1]]));
        $this->panel->setBoton('grid', new BotonImg('actividad.delete', [
            'where' => ['estado', '<', '2'],
            'data-confirm' => 'Segur que vols eliminar esta activitat?',
        ]));
        $this->panel->setBoton('profile', new BotonIcon('actividad.delete', [
            'class' => 'btn-danger',
            'where' => ['estado', '<', '2'],
            'data-confirm' => 'Segur que vols eliminar esta activitat?',
        ]));
        $this->panel->setBoton('grid', new BotonImg('actividad.ics', ['img' => 'fa-calendar', 'where' => ['desde', 'posterior', Date::yesterday()]]));
    }

    public function autorizar()
    {
        $activitats = Actividad::where('estado', '1')->get();
        if (file_exists(storage_path(env('services.calendar.calendarCredentialsPath')))) {
            $gC = new GoogleCalendarService();
            foreach ($activitats as $activitat){
                $assistents = $activitat->profesores()->select('email')->get()->toArray();
                $gC->addEvent(
                    $activitat->name,
                    $activitat->descripcion,
                    $activitat->desde,
                    $activitat->hasta,
                    $assistents
                );
            }
            $gC->saveEvents();
        }
        StateService::makeAll($activitats, 2);
        return back();
    }


    protected function accept($id, $redirect = true)
    {
        $stSrv = new StateService($this->class, $id);
        if (file_exists(storage_path(env('services.calendar.calendarCredentialsPath')))) {
            $gC = new GoogleCalendarService();
            $activitat = Actividad::find($id);
            $assistents = $activitat->profesores()->select('email')->get()->toArray();
            $gC->addEvent(
                $activitat->name,
                $activitat->descripcion,
                $activitat->desde,
                $activitat->hasta,
                $assistents
            );
            $gC->saveEvents();
        }
        $iniSta = $stSrv->getEstado();
        $finSta = $stSrv->putEstado($iniSta+1);
        if ($redirect) {
            return $this->follow($iniSta, $finSta);
        }
    }

    public function printAutoritzats(){
        return $this->imprimir('extraescolars');
    }

    public function itaca($id)
    {
        $elemento = $this->class::findOrFail($id);
        $elemento->estado = 5;
        $elemento->save();
        return $this->follow(4, 5);
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
        return (new GestorService(Actividad::findOrFail($id)))->render();
    }
}
