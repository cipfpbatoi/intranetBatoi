<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Intranet\Entities\Instructor;
use Intranet\Entities\Centro;
use Intranet\Entities\Fct;
use Intranet\Entities\Profesor;
use Response;
use DB;
use Styde\Html\Facades\Alert;
use Illuminate\Support\Facades\Session;
use Intranet\Botones\BotonImg;
use Jenssegers\Date\Date;



/**
 * Class InstructorController
 * @package Intranet\Http\Controllers
 */
class InstructorController extends IntranetController
{

    /**
     * @var string
     */
    protected $perfil = 'profesor';
    /**
     * @var string
     */
    protected $model = 'Instructor';
    /**
     * @var array
     */
    protected $titulo = [];
    /**
     * @var array
     */
    protected $gridFields = ['dni', 'nombre','email','Nfcts', 'TutoresFct','Xcentros','telefono'];
    /**
     * @var bool
     */
    protected $modal = false;
    
    use traitImprimir;

    /**
     *
     */
    public function iniBotones()
    {
        $this->panel->setBoton('grid', new BotonImg('instructor.edit'));
        $this->panel->setBoton('grid', new BotonImg('instructor.show'));
        $this->panel->setBoton('grid', new BotonImg('instructor.pdf'));
    }

    /**
     * @return mixed
     */
    public function search()
    {
        $instructores = [];
        foreach (Fct::misFcts()->get() as $fct){
            foreach ($fct->Colaboradores as $instructor){
                $instructores[] = $instructor->dni;
            }
        }    
        return Instructor::whereIn('dni',$instructores)->get();
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function show($id)
    {
        $empresa = Instructor::find($id)->Centros->first()->idEmpresa;
        return redirect("empresa/$empresa/detalle");
    }



    /**
     * @param $centro
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function crea($centro)
    {
        return parent::create();
    }

    /**
     * @param $id
     * @param $empresa
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edita($id, $empresa)
    {
        return parent::edit($id);
    }

    /**
     * @param Request $request
     * @param $id
     * @param $centro
     * @return \Illuminate\Http\RedirectResponse
     */
    public function guarda(Request $request, $id, $centro)
    {
        parent::update($request, $id);
        return $this->showEmpresa(Centro::find($centro)->idEmpresa);
    }

    private function showEmpresa($id){
        $colaboracion = Session::get('colaboracion')??null;
        if ($colaboracion){
            Session::put('pestana',4);
            return redirect()->action('ColaboracionController@show',['colaboracion'=> $colaboracion]);
        } else {
            Session::put('pestana',2);
            return redirect()->action('EmpresaController@show', ['empresa' => $id]);
        }
    }

    /**
     * @param Request $request
     * @param $centro
     * @return \Illuminate\Http\RedirectResponse
     */
    public function almacena(Request $request, $centro)
    {
        DB::transaction(function() use ($request,$centro) {
            $instructor = Instructor::find($request->dni);
            if (!$instructor){
                if (!$request->dni) {
                     $max = Instructor::where('dni','>', 'EU0000000')->where('dni','<','EU9999999')->max('dni');
                     $max = (int) substr($max, 2) +1;
                     $dni = 'EU'.str_pad($max, 7,'0', STR_PAD_LEFT);
                     $request->merge(['dni' => $dni]);
                }
                parent::store($request);
            }
            $instructor = Instructor::find($request->dni);
            $instructor->Centros()->syncWithoutDetaching($centro);
        });
        return $this->showEmpresa(Centro::find($centro)->idEmpresa);
    }

    /**
     * @param $id
     * @param $centro
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id, $centro)
    {
        $instructor = Instructor::find($id);
        $instructor->Centros()->detach($centro);
        if ($instructor->Centros()->count() == 0) {
            try {
                parent::destroy($id);
            } catch (\Exception $e) {
            };
        }

        return $this->showEmpresa(Centro::find($centro)->idEmpresa);
    }

    /**
     * @param $id
     * @param $idCentro
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function copy($id, $idCentro)
    {
        $instructor = Instructor::findOrFail($id);
        $centro = Centro::findOrFail($idCentro);
        $posibles = hazArray($centro->Empresa->centros,'id',['nombre','direccion'],'-');

        return view('instructor.copy',compact('instructor','posibles','centro'));
    }

    /**
     * @param Request $request
     * @param $id
     * @param $idCentro
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toCopy(Request $request, $id, $idCentro)
    {
        $instructor = Instructor::findOrFail($id);
        $instructor->Centros()->attach($request->centro);
        if ($request->accion == 'mou') {
            $instructor->Centros()->detach($idCentro);
        }

        return $this->showEmpresa(Centro::find($idCentro)->idEmpresa);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function pdf($id)
    {
        $instructor = Instructor::findOrFail($id);
        if ($instructor->surnames != ''){
            $fcts = $instructor->Fcts;
            $fecha = $this->ultima_fecha($fcts);
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
                'instructor' => $instructor
            ];
            if ($fcts->count()==1) {
                $pdf = $this->hazPdf('pdf.fct.instructor', $fcts->first(), $dades);
            }
            else
            {
                $centros = [];
                foreach ($fcts as $fct){
                    if (!in_array($fct->Colaboracion->idCentro, $centros)) {
                        $centros[] = $fct->Colaboracion->idCentro;
                    }
                }
                $pdf = $this->hazPdf('pdf.fct.instructors', $centros, $dades);
            }
            return $pdf->stream();
        }

        Alert::danger("Completa les dades de l'instructor");
        return redirect("/instructor");

    }

    /**
     * @param $fcts
     * @return \Date|Date|null
     */
    private function ultima_fecha($fcts)
    {
        $posterior = new Date();
        foreach ($fcts as $fct){
            $posterior = FechaPosterior($fct->hasta, $posterior);
        }
        return $posterior;
    }


}
