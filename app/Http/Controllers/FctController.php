<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;
use Intranet\Entities\Fct;
use Intranet\Entities\FctConvalidacion;
use Intranet\Entities\AlumnoFct;
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
    protected $gridFields = ['Centro','periode','desde', 'horas','nalumnes','Lalumnes','XInstructor'];
    protected $grupo;
    protected $vista = ['show' => 'fct'];
    

    protected $modal = false;

    use traitImprimir;

   
    
    public function edit($id)
    {
        $elemento = Fct::findOrFail($id);
        $elemento->setInputType('idAlumno', ['type' => 'hidden','disableAll' => 'disableAll']);
        $elemento->setInputType('idColaboracion', ['disabled' => 'disabled']);
        $default = $elemento->fillDefautOptions();
        $modelo = $this->model;
        
        return view($this->chooseView('edit'), compact('elemento', 'default', 'modelo'));
    }
    
    public function convalidacion()
    {
        $elemento = new FctConvalidacion();
        $default = $elemento->fillDefautOptions(); // ompli caracteristiques dels camps
        $modelo = $this->model;
        return view($this->chooseView('create'), compact('elemento', 'default', 'modelo'));
    }
    
 
    protected function iniBotones()
    {
        //$this->panel->setBotonera();
        $this->panel->setBoton('grid', new BotonImg('fct.delete',['where'=>['Nalumnes','==','1']]));
        $this->panel->setBoton('grid', new BotonImg('fct.convalidacion',['img' => 'fa-edit','where'=>['asociacion','==','2']]));
        $this->panel->setBoton('grid', new BotonImg('fct.edit',['where'=>['asociacion', '==', '1']]));
        $this->panel->setBoton('grid', new BotonImg('fct.show',['where'=>['asociacion', '==', '1']]));
        $this->panel->setBoton('grid', new BotonImg('fct.pdf',['where'=>['asociacion', '==', '1']]));
        $this->panel->setBoton('grid', new BotonImg('fct.pdfInstructor',['img'=>'fa-file-pdf-o','where'=>['asociacion', '==', '1']]));
        $this->panel->setBoton('grid', new BotonImg('fct.email',['orWhere'=>['correoAlumno','==','0','correoInstructor','==','0']]));
        $this->panel->setBoton('index', new BotonBasico("fct.create", ['class' => 'btn-info','roles' => config('roles.rol.tutor')]));
        $this->panel->setBoton('index', new BotonBasico("fct.pass", ['class' => 'btn-info','roles' => config('roles.rol.tutor')]));
        $this->panel->setBoton('index', new BotonBasico("fct.pg0301.print",['roles' => config('roles.rol.tutor')]));
        $this->panel->setBoton('index', new BotonBasico("fct.pr0301.print",['roles' => config('roles.rol.tutor')]));
        $this->panel->setBoton('index', new BotonBasico("fct.pr0401.print",['roles' => config('roles.rol.tutor')]));
        $this->panel->setBoton('index', new BotonBasico("fct.pr0402.print",['roles' => config('roles.rol.tutor')]));
        $this->panel->setBoton('index', new BotonBasico("fct.pr0601.print",['roles' => config('roles.rol.tutor')]));
        $find = Documento::where('propietario', AuthUser()->FullName)->where('tipoDocumento','Qualitat')
                ->where('curso',Curso())->first();
        if (!$find) $this->panel->setBoton('index', new BotonBasico("fct.upload", ['class' => 'btn-info','roles' => config('roles.rol.tutor')]));
        else $this->panel->setBoton('index', new BotonBasico("documento.$find->id.edit", ['class' => 'btn-info','roles' => config('roles.rol.tutor')]));
        Session::put('redirect', 'FctController@index');
    }

    

    public function search()
    {
        return Fct::misFcts()->esFct()->get();
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
        if (FCT::misFcts()->Activa(config("pr.$document.cuando"))->count()){
            return $this->hazPdf("pdf.fct.$document", AlumnoFct::misFcts(null,"pr.$document.cuando")->get(),
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
        $dades = ['date' => FechaString(Hoy()),
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
    
    public function pdfInstructor($id)
    {
        $fct = Fct::findOrFail($id);
        $instructor = $fct->Instructor;
        if ($instructor->surnames != ''){
            $fecha = Hoy();
            $secretario = Profesor::find(config('contacto.secretario'));
            $director = Profesor::find(config('contacto.director'));
            $dades = ['date' => FechaString($fecha,'ca'),
                'fecha' => FechaString($fecha,'es'),
                'consideracion' => $secretario->sexo === 'H' ? 'En' : 'Na',
                'secretario' => $secretario->FullName,
                'centro' => config('contacto.nombre'),
                'poblacion' => config('contacto.poblacion'),
                'provincia' => config('contacto.provincia'),
                'director' => $director->FullName,
                'instructor' => $instructor
            ];
            $pdf = $this->hazPdf('pdf.fct.instructors', $fct, $dades);
            return $pdf->stream();
        }
        else
        {
            Alert::danger("Completa les dades de l'instructor");
            return back();   
        }
        
    }
    public function anexevii($id)
    {
        $fct = Fct::findOrFail($id);
        $secretario = Profesor::find(config('contacto.secretario'));
        $director = Profesor::find(config('contacto.director'));
        $dades = ['date' => FechaString(FechaPosterior($fct->hasta)),
            'consideracion' => $secretario->sexo === 'H' ? 'En' : 'Na',
            'secretario' => $secretario->FullName,
            'centro' => config('contacto.nombre'),
            'codigo' => config('contacto.codi'),
            'poblacion' => config('contacto.poblacion'),
            'provincia' => config('contacto.provincia'),
            'director' => $director->FullName
        ];
        
        $pdf = $this->hazPdf('dual.anexe_vii', $fct,$dades,'landscape','a4',10);
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
        
        return $this->redirect();
    }

   
    
    public function store(Request $request)
    {
        $idFct = DB::transaction(function() use ($request){
            $idAlumno = $request['idAlumno'];
            $elemento = Fct::where('idColaboracion',$request->idColaboracion)
                    ->where('asociacion',$request->asociacion)
                    ->where('idInstructor',$request->idInstructor)
                    ->where('desde', FechaInglesa($request->desde))->get()->first();
            if (!$elemento){
                $elemento = new Fct();
                $this->validateAll($request, $elemento);
                unset($request['idAlumno']);
                $id = $elemento->fillAll($request);
            } else $id = $elemento->id;
            //dd(Colaboracion::find($request->idColaboracion)->Centro->Instructores->count());
            if ($elemento->asociacion == 2)
                $elemento->Alumnos()->attach($idAlumno,['calificacion' => 2]);
            else
                $elemento->Alumnos()->attach($idAlumno);
            
            return $id;
        });
        
        return $this->redirect();
    }
    
    public function show($id)
    {
        $activa = Session::get('pestana') ? Session::get('pestana') : 1;
        $fct = Fct::findOrFail($id);
        $instructores = $fct->Colaboradores->pluck('dni');
        return view($this->chooseView('show'), compact('fct', 'activa','instructores'));
    }


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
    
    public function nouAlumno($idFct,Request $request){
        
        $fct = Fct::find($idFct);
        $fct->Alumnos()->attach($request->idAlumno,['calificacion'=>0,'calProyecto'=>0,'actas'=>0,'insercion'=>0]);
        
        return back();
    }
    
    public function nouInstructor($idFct,Request $request){
       $fct = Fct::find($idFct);
       $fct->Colaboradores()->attach($request->idInstructor,['horas'=>$request->horas]); 
       return back();
    }
    public function deleteInstructor($idFct,$idInstructor){
       $fct = Fct::find($idFct);
       $fct->Colaboradores()->detach($idInstructor); 
       return back();
    }
    public function modificaHoras($idFct,Request $request){
        $fct = Fct::find($idFct);
        foreach ($request->except('_token') as $dni => $horas){
            $fct->Colaboradores()->updateExistingPivot($dni, ['horas'=>$horas]);
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
