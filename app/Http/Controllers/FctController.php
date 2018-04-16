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
        $elemento->deleteInputType('idInstructor');
        $default = $elemento->fillDefautOptions();
        $modelo = $this->model;
        
        return view($this->chooseView('edit'), compact('elemento', 'default', 'modelo'));
    }
 
    protected function iniBotones()
    {
        //$this->panel->setBotonera();
        $this->panel->setBoton('grid', new BotonImg('fct.delete',['where'=>['calificacion', '<', '1']]));
        $this->panel->setBoton('grid', new BotonImg('fct.edit'));
        $this->panel->setBoton('grid', new BotonImg('fct.show'));
        $this->panel->setBoton('grid', new BotonImg('fct.pdf'));
        $this->panel->setBoton('grid', new BotonImg('fct.email'));
        $this->panel->setBoton('index', new BotonBasico("fct.create", ['class' => 'btn-info','roles' => config('constants.rol.tutor')]));
        $this->panel->setBoton('index', new BotonBasico("fct.pg0301.print",['roles' => config('constants.rol.tutor')]));
        $this->panel->setBoton('index', new BotonBasico("fct.pr0301.print",['roles' => config('constants.rol.tutor')]));
        $this->panel->setBoton('index', new BotonBasico("fct.pr0401.print",['roles' => config('constants.rol.tutor')]));
        $this->panel->setBoton('index', new BotonBasico("fct.pr0402.print",['roles' => config('constants.rol.tutor')]));
        $this->panel->setBoton('index', new BotonBasico("fct.pr0601.print",['roles' => config('constants.rol.tutor')]));
        
        
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
                    if ($fct->calProyecto) {
                        $fct->actas = 3;
                        $testigo = true;
                        $fct->save();
                    }
                } else {
                    if ($fct->calificacion) {
                        $fct->actas = 3;
                        $testigo = true;
                        $fct->save();
                    }
                }
            }
            if ($testigo) {
                $grupo->acta_pendiente = 1;
                $grupo->save();
                avisa(config('constants.contacto.jefeEstudios2'), "Acta pendent grup $grupo->nombre", "http://intranet.cipfpbatoi.es/direccion/$grupo->codigo/acta");
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
        $fct = $this->class::findOrFail($id);
        $secretario = Profesor::find(config('constants.contacto.secretario'));
        $director = Profesor::find(config('constants.contacto.director'));
        $dades = ['date' => FechaString(FechaPosterior($fct->hasta)),
            'consideracion' => $secretario->sexo === 'H' ? 'En' : 'Na',
            'secretario' => $secretario->FullName,
            'centro' => config('constants.contacto.nombre'),
            'poblacion' => config('constants.contacto.poblacion'),
            'provincia' => config('constants.contacto.provincia'),
            'director' => $director->FullName
        ];
        $pdf = $this->hazPdf('pdf.fct.fct', $fct, $dades);
        return $pdf->stream();
    }

    
    
    public function Acta($idGrupo){
        $grupo = Grupo::findOrFail($idGrupo);
        $fcts = Fct::Grupo($grupo)->Pendiente()->get();
        $panel = new Panel($this->model, ['Nombre', 'Centro', 'hasta', 'horas', 'qualificacio', 'projecte'], 'grid.standard');
        $panel->setTitulo(['quien' => $grupo->nombre ]);
        $panel->setElementos($fcts);
        $panel->setBoton('index', new BotonBasico("direccion.$idGrupo.finActa",['text'=>'acta']));
        return view($this->chooseView('list'), ['panel' => $panel]);
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
        if (($elemento->Colaboracion->email != '') && ($elemento->Alumno->email != '')) {
            dispatch(new SendEmail($elemento->Colaboracion->email, $remitente, 'email.fct.instructor', $elemento));
            dispatch(new SendEmail($elemento->Alumno->email, $remitente, 'email.fct.alumno', $elemento));
            Alert::info('Correus processats');
        } else
            Alert::info("O L'alumne o la col.laboració no tenen correu. Revisa-ho");

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
            $idInstructor = $request->idInstructor;
            $idFct = parent::realStore(subsRequest($request->duplicate(null, $request->except('idInstructor')),[]));
            $fct = Fct::find($idFct);
            $fct->Instructores()->attach($idInstructor,['horas'=>$fct->horas]);
            return $idFct;
        });
        if (Session::get('pestana')){
            Session::put('pestana',3);
            return redirect()->action('EmpresaController@show', ['id' => Colaboracion::find($request->idColaboracion)->Centro->idEmpresa]);
        }
        else return $this->show($idFct);
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
        $empresa = Fct::find($id)->Colaboracion->Centro->idEmpresa;
        parent::destroy($id);
        if (Session::get('pestana')){
            Session::put('pestana',3);
            return redirect()->action('EmpresaController@show', ['id' => $empresa]);
        }
        else return $this->redirect();
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
   

}
