<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;
use Intranet\Entities\Fct;
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

/**
 * Class FctController
 * @package Intranet\Http\Controllers
 */
class FctController extends IntranetController
{

    /**
     * @var string
     */
    protected $perfil = 'profesor';
    /**
     * @var string
     */
    protected $model = 'Fct';
    /**
     * @var array
     */
    protected $gridFields = ['Centro','periode','XInstructor','Lalumnes','Nalumnes'];
    /**
     * @var
     */
    protected $grupo;
    /**
     * @var array
     */
    protected $vista = ['show' => 'fct'];


    /**
     * @var bool
     */
    protected $modal = false;

    use traitImprimir;


    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id)
    {
        $elemento = Fct::findOrFail($id);
        $elemento->setInputType('idAlumno', ['type' => 'hidden','disableAll' => 'disableAll']);
        $elemento->setInputType('idColaboracion', ['disabled' => 'disabled']);
        $elemento->setInputType('desde',['type'=>'hidden']);
        $elemento->setInputType('hasta',['type'=>'hidden']);
        $elemento->setInputType('horas',['type'=>'hidden']);
        $default = $elemento->fillDefautOptions();
        $modelo = $this->model;
        
        return view($this->chooseView('edit'), compact('elemento', 'default', 'modelo'));
    }


    /**
     *
     */
    protected function iniBotones()
    {
        $this->panel->setBoton('grid', new BotonImg('fct.edit',['where'=>['asociacion','==','1']]));
        $this->panel->setBoton('grid', new BotonImg('fct.delete',['where'=>['Nalumnes','==','1']]));
        $this->panel->setBoton('grid', new BotonImg('fct.show',['where'=>['asociacion', '==', '1']]));
        $this->panel->setBoton('grid', new BotonImg('fct.pdf',['img'=>'fa-file-pdf-o','where'=>['asociacion', '==', '1']]));
        //$this->panel->setBoton('grid', new BotonImg('fct.email',['where'=>['correoInstructor','==','0']]));
        $this->panel->setBoton('index', new BotonBasico("fct.create", ['class' => 'btn-info','roles' => config('roles.rol.tutor')]));
        $this->panel->setBoton('index', new BotonBasico("fct.pg0301.print",['roles' => config('roles.rol.tutor')]));
        $this->panel->setBoton('index', new BotonBasico("fct.pr0301.print",['roles' => config('roles.rol.tutor')]));
        $this->panel->setBoton('index', new BotonBasico("fct.pr0401.print",['roles' => config('roles.rol.tutor')]));
        $this->panel->setBoton('index', new BotonBasico("fct.pr0402.print",['roles' => config('roles.rol.tutor')]));
        $this->panel->setBoton('index', new BotonBasico("fct.pr0601.print",['roles' => config('roles.rol.tutor')]));
        Session::put('redirect', 'FctController@index');
    }


    /**
     * @return mixed
     */
    public function search()
    {
        return Fct::misFcts()->esFct()->get();
    }

    /**
     * @param $document
     * @return \Illuminate\Http\RedirectResponse
     */
    public function document($document)
    {
        return $this->printDocument($document,$this->quienSaleDocumento(config("pr.$document.cuando")));
    }

    /**
     * @param $document
     * @param $quienes
     * @return \Illuminate\Http\RedirectResponse
     */
    private function printDocument($document, $quienes){
        if ($quienes->count())
            return $this->hazPdf("pdf.fct.$document", $quienes,
                config("pr.$document"), config("pr.$document.orientacion"))->stream();

        Alert::message('No tens alumnes per a eixa documentació','warning');
        return back();

    }

    /**
     * @param $tipoDocumento
     * @return mixed
     */
    private function quienSaleDocumento($tipoDocumento){
        if ($tipoDocumento == 1) return AlumnoFct::misFcts()->where('pg0301',0)->orderBy('idAlumno')->orderBy('desde')->get();
        if ($tipoDocumento == 2) return AlumnoFct::misFcts()->where('desde','<=',Hoy())->where('hasta','>=',Hoy())->orderBy('idAlumno')->orderBy('desde')->get();
        return Alumno::misAlumnos()->orderBy('apellido1')->orderBy('apellido2')->get();

    }

    /**
     * @param Request $request
     * @param $document
     * @return \Illuminate\Http\RedirectResponse
     */
    public function documentPost(Request $request, $document)
    {
        return $this->printDocument($document,
            AlumnoFct::misFcts()->where('desde','<=',FechaInglesa($request->hasta))
                ->where('hasta','>=',FechaInglesa($request->desde))->orderBy('idAlumno')->orderBy('desde')->get());

    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function pdf($id)
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



    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        parent::update($request, $id);
        return $this->redirect();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $idFct = DB::transaction(function() use ($request){
            $idAlumno = $request['idAlumno'];
            $hasta = $request['hasta'];
            $elementos = Fct::where('idColaboracion',$request->idColaboracion)
                    ->where('asociacion',$request->asociacion)
                    ->where('idInstructor',$request->idInstructor)
                    ->get();
            $id = null;
            foreach ($elementos as $elemento){
                    if ($elemento->Periode == PeriodePractiques($request->desde)){
                        $id = $elemento->id;
                        break;
                    }
                }
            if (!$id){ 
                $elemento = new Fct();
                $this->validateAll($request, $elemento);
                $id = $elemento->fillAll($request);
            } 
            $elemento->Alumnos()->attach($idAlumno,['desde'=> FechaInglesa($request->desde),'hasta'=>FechaInglesa($hasta),'horas'=>$request->horas]);
            
            return $id;
        });
        
        return $this->redirect();
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id)
    {
        $activa = Session::get('pestana') ? Session::get('pestana') : 1;
        $fct = Fct::findOrFail($id);
        $instructores = $fct->Colaboradores->pluck('dni');
        return view($this->chooseView('show'), compact('fct', 'activa','instructores'));
    }


    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
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

    /**
     * @param $idFct
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function nouAlumno($idFct, Request $request){
        
        $fct = Fct::find($idFct);
        $fct->Alumnos()->attach($request->idAlumno,['calificacion'=>0,'calProyecto'=>0,'actas'=>0,'insercion'=>0,
            'desde'=> FechaInglesa($request->desde),'hasta'=> FechaInglesa($request->hasta),'horas'=>$request->horas]);
        
        return back();
    }

    /**
     * @param $idFct
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function nouInstructor($idFct, Request $request){
       $fct = Fct::find($idFct);
       $fct->Colaboradores()->attach($request->idInstructor,['horas'=>$request->horas]); 
       return back();
    }

    /**
     * @param $idFct
     * @param $idInstructor
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteInstructor($idFct, $idInstructor){
       $fct = Fct::find($idFct);
       $fct->Colaboradores()->detach($idInstructor); 
       return back();
    }

    /**
     * @param $idFct
     * @param $idAlumno
     * @return \Illuminate\Http\RedirectResponse
     */
    public function alumnoDelete($idFct, $idAlumno){
       $fct = Fct::find($idFct);
       $fct->Alumnos()->detach($idAlumno); 
       return back();
    }

    /**
     * @param $idFct
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function modificaHoras($idFct, Request $request){
        $fct = Fct::find($idFct);
        foreach ($request->except('_token') as $dni => $horas){
            $fct->Colaboradores()->updateExistingPivot($dni, ['horas'=>$horas]);
        }
        return back();
    }
    
   
    
   

}
