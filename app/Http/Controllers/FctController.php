<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Intranet\Entities\Fct;
use Intranet\Entities\Profesor;
use Intranet\Botones\BotonImg;
use Illuminate\Support\Facades\Session;
use Intranet\Services\FDFPrepareService;
use Intranet\Services\FormBuilder;
use Styde\Html\Facades\Alert;
use Intranet\Botones\BotonBasico;



/**
 * Class FctController
 * @package Intranet\Http\Controllers
 */
class FctController extends IntranetController
{
    const ROLES_ROL_TUTOR = 'roles.rol.tutor';

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
    protected $gridFields = ['Centro','periode','Contacto','Lalumnes','Nalumnes'];
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



    public function edit($id)
    {
        $formulario = new FormBuilder(Fct::findOrFail($id),['idInstructor' => ['type'=>'select']]);
        $modelo = $this->model;
        return view($this->chooseView('edit'), compact('formulario', 'modelo'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $fct = Fct::findOrFail($id);
        $fct->idInstructor = $request->idInstructor;
        $fct->save();
        return $this->redirect();
    }


    /**
     *
     */
    protected function iniBotones()
    {
        $this->panel->setBoton('grid', new BotonImg('fct.edit',['where'=>['asociacion','==','1']]));
        $this->panel->setBoton('grid', new BotonImg('fct.show',['where'=>['asociacion', '==', '1']]));
        $this->panel->setBoton('grid', new BotonImg('fct.pdf',['img'=>'fa-file-pdf-o','where'=>['asociacion', '==', '1']]));
        $this->panel->setBoton('index', new BotonBasico("alumnofct", ['class' => 'btn-info','roles' => config(self::ROLES_ROL_TUTOR)]));
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
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function pdf($id,Request $request)
    {
        $fct = Fct::findOrFail($id);
        $instructor = $fct->Instructor;
        if (isset($instructor->surnames)) {
            return self::preparePdf($fct,$request->fecha,$request->horas)->stream();
        } else {
            Alert::danger("Completa les dades de l'instructor");
            return back();   
        }
        
    }

    public function certificat($id){
        $pdf['fdf'] = '13_Certificado_persona_instructora.pdf';
        $pdf['method'] = 'certInstructor';
        return response()->file(FDFPrepareService::stampPDF($pdf,Fct::findOrFail($id),'signatura_DS.pdf'));
    }

    /*
    public function certificat($id)
    {
        $fct = Fct::findOrFail($id);
        $instructor = $fct->Instructor;
        if (isset($instructor->surnames)) {
            return self::preparePdf($fct,$fct->hasta,$fct->AlFct->max('horas'))->stream();
        } else {
            Alert::danger("Completa les dades de l'instructor");
            return back();
        }

    }*/

    public static function preparePdf($fct,$fecha,$horas)
    {
        $secretario = Profesor::find(config(fileContactos().'.secretario'));
        $director = Profesor::find(config(fileContactos().'.director'));
        $dades = ['date' => FechaString($fecha,'ca'),
            'fecha' => FechaString($fecha,'es'),
            'consideracion' => $secretario->sexo === 'H' ? 'En' : 'Na',
            'secretario' => $secretario->FullName,
            'centro' => config('contacto.nombre'),
            'poblacion' => config('contacto.poblacion'),
            'provincia' => config('contacto.provincia'),
            'director' => $director->FullName,
            'instructor' => $fct->Instructor,
            'horas' => $horas
        ];

        return self::hazPdf('pdf.fct.certificatInstructor', $fct, $dades);
    }





    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {

        DB::transaction(function() use ($request){
            $idAlumno = $request['idAlumno'];
            $hasta = $request['hasta'];
            $fct = Fct::where('idColaboracion',$request->idColaboracion)
                    ->where('asociacion',$request->asociacion)
                    ->where('idInstructor',$request->idInstructor)
                    ->where('periode',$request->periode)
                    ->first();

            if (!$fct) {
                $fct = new Fct();
                $this->validateAll($request, $fct);
                $fct->fillAll($request);
            }
            try {
                $fct->Alumnos()->attach($idAlumno,['desde'=> FechaInglesa($request->desde),'hasta'=>FechaInglesa($request->hasta),'horas'=>$request->horas,'autorizacion'=>$request->autorizacion]);
            } catch (\Exception $e)
            {
               Alert::danger("L'alumne $idAlumno ja té una Fct oberta amb eixa empresa ");
            }

            return $fct;
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
            return redirect()->action('EmpresaController@show', ['empresa' => $empresa]);
        } else {
            return parent::destroy($id);
        }
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
    public function nouFctAlumno(Request $request){
        if (isset($request->idInstructor)) {
            $this->store($request);
        } else {
            Alert::danger('No hi ha instructor.No puc generar la FCT');
        }

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
