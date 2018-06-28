<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;
use Intranet\Entities\Fct;
use Intranet\Entities\Alumno;
use Intranet\Entities\Grupo;
use Intranet\Entities\Profesor;
use Intranet\Entities\Colaboracion;
use Intranet\Entities\Empresa;
use Intranet\Botones\BotonImg;
use Jenssegers\Date\Date;
use Illuminate\Support\Facades\Session;
use Intranet\Jobs\SendEmail;
use Styde\Html\Facades\Alert;
use Intranet\Botones\BotonBasico;
use Intranet\Botones\Panel;
use Intranet\Entities\Documento;

class FctController extends IntranetController
{

    protected $perfil = 'profesor';
    protected $model = 'Fct';
    protected $gridFields = [ 'Nombre', 'Centro','desde', 'fin', 'periode','qualificacio', 'projecte','horas','desde','hasta','id'];
    protected $grupo;
    protected $vista = ['show' => 'fct'];
    

    protected $modal = false;

    use traitImprimir;

    public function index(){
        Session::forget('pestana');
        return parent::index();
    }
    
    public function edit($id)
    {
        $elemento = Fct::findOrFail($id);
        $elemento->setInputType('idAlumno', ['disabled' => 'disabled']);
        $elemento->setInputType('idColaboracion', ['disabled' => 'disabled']);
        $default = $elemento->fillDefautOptions();
        $modelo = $this->model;
        
        return view($this->chooseView('edit'), compact('elemento', 'default', 'modelo'));
    }
    public function pass()
    {
        $elemento = new Fct();
        $elemento->asociacion = 3;
        $elemento->horas = 0;
        $elemento->desde = Hoy();
        $elemento->hasta = Hoy();
        $elemento->horas_semanales = 1;
        $elemento->calificacion = 2;
        $elemento->correoAlumno = 1;
        $elemento->correoInstructor = 1;
        //crea un nou element del model
        $elemento->setInputType('idColaboracion',['disableAll' => 'on']);
        $elemento->setInputType('hasta',['type' => 'hidden']);
        $elemento->setInputType('horas',['type' => 'hidden']);
        $elemento->setInputType('horas_semanales',['type' => 'hidden']);
        $default = $elemento->fillDefautOptions(); // ompli caracteristiques dels camps
        $modelo = $this->model;
        return view($this->chooseView('create'), compact('elemento', 'default', 'modelo'));
    }
 
    protected function iniBotones()
    {
        //$this->panel->setBotonera();
        $this->panel->setBoton('grid', new BotonImg('fct.delete',['orWhere'=>['calificacion', '<', '1','asociacion','>',2]]));
        $this->panel->setBoton('grid', new BotonImg('fct.edit',['where'=>['asociacion', '==', '1']]));
        $this->panel->setBoton('grid', new BotonImg('fct.show',['where'=>['asociacion', '==', '1']]));
        $this->panel->setBoton('grid', new BotonImg('fct.pdf',['where'=>['asociacion', '==', '1']]));
        $this->panel->setBoton('grid', new BotonImg('fct.email',['orWhere'=>['correoAlumno','==','0','correoInstructor','==','0']]));
        $this->panel->setBoton('index', new BotonBasico("fct.create", ['class' => 'btn-info','roles' => config('constants.rol.tutor')]));
        $this->panel->setBoton('index', new BotonBasico("fct.pass", ['class' => 'btn-info','roles' => config('constants.rol.tutor')]));
        $this->panel->setBoton('index', new BotonBasico("fct.pg0301.print",['roles' => config('constants.rol.tutor')]));
        $this->panel->setBoton('index', new BotonBasico("fct.pr0301.print",['roles' => config('constants.rol.tutor')]));
        $this->panel->setBoton('index', new BotonBasico("fct.pr0401.print",['roles' => config('constants.rol.tutor')]));
        $this->panel->setBoton('index', new BotonBasico("fct.pr0402.print",['roles' => config('constants.rol.tutor')]));
        $this->panel->setBoton('index', new BotonBasico("fct.pr0601.print",['roles' => config('constants.rol.tutor')]));
        $find = Documento::where('propietario', AuthUser()->FullName)->where('tipoDocumento','Qualitat')
                ->where('curso',Curso())->first();
        if (!$find) $this->panel->setBoton('index', new BotonBasico("fct.upload", ['class' => 'btn-info','roles' => config('constants.rol.tutor')]));
        else $this->panel->setBoton('index', new BotonBasico("documento.$find->id.edit", ['class' => 'btn-info','roles' => config('constants.rol.tutor')]));
        Session::put('redirect', 'FctController@index');
    }

    

    public function search()
    {
        return Fct::misFcts()->get();
    }
    
    protected function apte($id)
    {
        $fct = Fct::findOrFail($id);
        $fct->calificacion = 1;
        $fct->save();
        return back();
    }

    protected function noApte($id)
    {
        $fct = Fct::findOrFail($id);
        $fct->calificacion = 0;
        $fct->calProyecto = null;
        $fct->save();
        return back();
    }
    
    protected function noProyecto($id)
    {
        $fct = Fct::findOrFail($id);
        $fct->calProyecto = 0;
        $fct->save();
        return back();
    }
    protected function nuevoProyecto($id)
    {
        $fct = Fct::findOrFail($id);
        $fct->calProyecto = null;
        $fct->actas = 1;
        $fct->save();
        return back();
    }
    protected function modificaNota($id){
        $elemento = Fct::findOrFail($id);
        $elemento->setInputType('idAlumno', ['type' => 'hidden']);
        $elemento->setInputType('idColaboracion',['type' => 'hidden']);
        $elemento->setInputType('desde',['type' => 'hidden']);
        $elemento->setInputType('hasta',['type' => 'hidden']);
        $elemento->setInputType('horas',['type' => 'hidden']);
        $elemento->setInputType('asociacion',['type' => 'hidden']);
        $elemento->setInputType('horas_semanales',['type' => 'hidden']);
        $elemento->setInputType('calProyecto', ['type' => 'text']);
        $default = $elemento->fillDefautOptions();
        $modelo = $this->model;
        
        return view($this->chooseView('edit'), compact('elemento', 'default', 'modelo'));
    }
    
    
    public function demanarActa()
    {
        $grupo = Grupo::QTutor()->first();
        if ($grupo->acta_pendiente)
            Alert::message("L'acta pendent esta en procés", 'info');
        else {
            $fcts = Fct::MisFcts()->NoAval()->get();
            $testigo = false;
            foreach ($fcts as $fct) {
                if ($grupo->proyecto) {
                    if (isset($fct->calProyecto)) {
                        $fct->actas = 3;
                        $testigo = true;
                        $fct->save();
                    }
                } else {
                    if (isset($fct->calificacion)) {
                        $fct->actas = 3;
                        $testigo = true;
                        $fct->save();
                    }
                }
            }
            if ($testigo) {
                $grupo->acta_pendiente = 1;
                $grupo->save();
                avisa(config('contacto.jefeEstudios2'), "Acta pendent grup $grupo->nombre", config('contacto.host.web')."/direccion/$grupo->codigo/acta");
                Alert::message('Acta demanada', 'info');
            } else
                Alert::message('No tens nous alumnes per ser avaluats', 'warning');
        }
        return back();
    }

    public function document($document)
    {
        //dd(FCT::misFcts()->Activa(config("pr.$document.cuando")));
        if (FCT::misFcts()->Activa(config("pr.$document.cuando"))->count()){
            return $this->hazPdf("pdf.fct.$document", FCT::misFcts()->Activa(config("pr.$document.cuando"))->get(),
                    config("pr.$document"), config("pr.$document.orientacion"))->stream();
        }
        else{
            Alert::message('No tens alumnes fent la FCT','warning');
            return back();
        }    
    }

    public function pdf($id)
    {
        $fct = Fct::findOrFail($id);
        $secretario = Profesor::find(config('contacto.secretario'));
        $director = Profesor::find(config('contacto.director'));
        $dades = ['date' => FechaString(FechaPosterior($fct->hasta)),
            'consideracion' => $secretario->sexo === 'H' ? 'En' : 'Na',
            'secretario' => $secretario->FullName,
            'centro' => config('contacto.nombre'),
            'poblacion' => config('contacto.poblacion'),
            'provincia' => config('contacto.provincia'),
            'director' => $director->FullName
        ];
        
        $pdf = $this->hazPdf('pdf.fct.alumne', $fct, $dades);
        return $pdf->stream();
    }

    
    public function finActa($idGrupo){
        $grupo = Grupo::findOrFail($idGrupo);
        $fcts = Fct::Grupo($grupo)->Pendiente()->get();
        foreach ($fcts as $fct){
            $fct->actas = 2;
            $fct->save();
        }
        $grupo->acta_pendiente = 0;
        $grupo->save();
        avisa($grupo->tutor, "Ja pots passar a arreplegar l'acta del grup $grupo->nombre", "#");
        return back();
    }

    public function email($id)
    {
        // CARREGANT DADES
        $elemento = Fct::findOrFail($id);
        $remitente = ['email' => AuthUser()->email, 'nombre' => AuthUser()->FullName, 'id' => AuthUser()->dni];

        // MANE ELS TREBALLS
        if ($elemento->Alumno->email != ''){
            $falta = false;
            foreach ($elemento->Instructores as $instructor){
                if ($instructor->email == '') {
                    $falta = true;
                    Alert::info("L'instructor $instructor->nombre no té correu. Revisa-ho");
                }
            }
            if (!$falta){
                dispatch(new SendEmail($elemento->Alumno->email, $remitente, 'email.fct.alumno', $elemento));
                dispatch(new SendEmail(AuthUser()->email, $remitente, 'email.fct.tutor', $elemento));
                foreach ($elemento->Instructores as $instructor){
                    dispatch(new SendEmail($instructor->email, $remitente, 'email.fct.instructor', $elemento));
                }
                Alert::info('Correus processats');
            }
        } else Alert::info("L'alumne no té correu. Revisa-ho");

        return back();
    }
    
    public function update(Request $request, $id)
    {
        parent::update($request, $id);
        
        if (Session::get('pestana')){
            Session::put('pestana',3);
            return redirect()->action('EmpresaController@show', ['id' => Colaboracion::find($request->idColaboracion)->Centro->idEmpresa]);
        }
        else return $this->redirect();
    }

    public function store(Request $request)
    {
        $idFct = DB::transaction(function() use ($request){
            //dd(Colaboracion::find($request->idColaboracion)->Centro->Instructores->count());
            $idFct = parent::realStore($request);
            
            if (isset($request->idColaboracion) && Colaboracion::find($request->idColaboracion)->Centro->Instructores->count() == 1){
                $idInstructor = Colaboracion::find($request->idColaboracion)->Centro->Instructores->first()->dni;
                $fct = Fct::find($idFct);
                $fct->Instructores()->attach($idInstructor,['horas'=>$fct->horas]);
            }
            return $idFct;
        });
        if (isset($request->idColaboracion))
            if (Session::get('pestana')){
                Session::put('pestana',3);
                return redirect()->action('EmpresaController@show', ['id' => Colaboracion::find($request->idColaboracion)->Centro->idEmpresa]);
            }
            else return redirect()->action('FctController@show', ['id' => $idFct ]);
        else
            return $this->redirect();
    }
    
    public function show($id)
    {
        $activa = Session::get('pestana') ? Session::get('pestana') : 1;
        $fct = Fct::findOrFail($id);
        $proyecto = Documento::where('propietario',$fct->Alumno->FullName)->first();
        $instructores = $fct->Instructores->pluck('dni');
        return view($this->chooseView('show'), compact('fct', 'activa','proyecto','instructores'));
    }
//    public function store(Request $request)
//    {
//        $idFct = parent::realtore($request);
//        $fct = Fct::find($idFct);
//        $fct->Instructores()->attach($fct->idInstructor,['horas'=>$fct->horas]);
//        if (Session::get('pestana')){
//            Session::put('pestana',3);
//            return redirect()->action('EmpresaController@show', ['id' => Colaboracion::find($request->idColaboracion)->Centro->idEmpresa]);
//        }
//        else return $this->redirect();
//    }

    public function destroy($id)
    {
        if (Session::get('pestana')){
            $empresa = Fct::find($id)->Colaboracion->Centro->idEmpresa;
            parent::destroy($id);
            Session::put('pestana',3);
            return redirect()->action('EmpresaController@show', ['id' => $empresa]);
        }
        else return parent::destroy($id);
    }
    public function nouInstructor($idFct,Request $request){
       $fct = Fct::find($idFct);
       $fct->Instructores()->attach($request->idInstructor,['horas'=>$request->horas]); 
       return back();
    }
    public function deleteInstructor($idFct,$idInstructor){
       $fct = Fct::find($idFct);
       $fct->Instructores()->detach($idInstructor); 
       return back();
    }
    public function modificaHoras($idFct,Request $request){
        $fct = Fct::find($idFct);
        foreach ($request->except('_token') as $dni => $horas){
            $fct->Instructores()->updateExistingPivot($dni, ['horas'=>$horas]);
        }
        return back();
    }
    
    public function empresa($id){
       $fct = Fct::find($id);
       $fct->insercion = $fct->insercion?0:1;
       $fct->save();
       return $this->redirect();
    }
    
   

}
