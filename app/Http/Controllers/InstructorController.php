<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Intranet\Entities\Colaboracion;
use Intranet\Entities\Instructor;
use Intranet\Entities\Centro;
use Intranet\Entities\Centro_instructor;
use Intranet\Entities\Ciclo;
use Intranet\Entities\Empresa;
use Intranet\Entities\Fct;
use Intranet\Entities\Profesor;
use Response;
use Exception;
use DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Styde\Html\Facades\Alert;
use Illuminate\Support\Facades\Session;
use Intranet\Botones\BotonImg;
use Jenssegers\Date\Date;


class InstructorController extends IntranetController
{

    protected $perfil = 'profesor';
    protected $model = 'Instructor';
    protected $titulo = [];
    protected $gridFields = ['dni', 'nombre','email','Nfcts', 'TutoresFct','Xcentros','telefono'];
    protected $modal = true;
    
    use traitImprimir;
    
    public function iniBotones()
    {
        $this->panel->setBoton('grid', new BotonImg('instructor.edit'));
        $this->panel->setBoton('grid', new BotonImg('instructor.show'));
        $this->panel->setBoton('grid', new BotonImg('instructor.pdf'));
    }
    
    public function search()
    {
        $fcts = Fct::misFcts()->get();
        $instructores = [];
        foreach ($fcts as $fct){
            foreach ($fct->Instructores as $instructor){
                $instructores[] = $instructor->dni;
            }
        }    
        return Instructor::whereIn('dni',$instructores)->get();
    }
    
    public function show($id)
    {
        $empresa = Instructor::find($id)->Centros->first()->idEmpresa;
        return redirect("empresa/$empresa/detalle");
    }

    public function load()
    {
        foreach (Colaboracion::all()->groupBy('idCentro') as $grupos) {
            foreach ($grupos->groupBy('idCiclo') as $grupo)
                DB::transaction(function() use ($grupo) {
                    $idColaboracion = $grupo->first()->id;
                    foreach ($grupo as $colaboracion) {
                        foreach ($colaboracion->fcts as $fct) {
                            Alert::message($fct->idAlumno, 'danger');
                            $fct->idColaboracion = $idColaboracion;
                            $this->nouInstructor($fct->idInstructor, $fct->instructor, $fct->Colaboracion->email, $fct->Colaboracion->telefono, $fct->Colaboracion->idCentro);
                            $fct->save();
                        }
                        $this->nouInstructor($colaboracion->dni, $colaboracion->instructor, $colaboracion->email, $colaboracion->telefono, $colaboracion->idCentro);
                        if ($colaboracion->id != $idColaboracion) {
                            Alert::message($colaboracion->id . ' canvia a ' . $idColaboracion, 'info');
                            $colaboracion->delete();
                        }
                    }
                });
        }
        Alert::message('FIN','success');
        return back();
    }

    private function nouInstructor($dni, $nom, $email, $telefono, $centro)
    {
        $dni = substr($dni,0,10);
        $instructor = Instructor::find($dni);
        if (!$instructor) {
            if ($dni) {
                $instructor = new Instructor();
                $instructor->dni = $dni;
                $instructor->nombre = substr($nom,0,60);
                $instructor->email = substr($email,0,60);
                $instructor->telefono = substr($telefono,0,20);
                $instructor->save();
                Alert::message('Nou instructor '.$instructor->dni,'info');
                $this->nouCentreInstructor($centro, $dni);
            }
        } else {
            $this->nouCentreInstructor($centro, $dni);
        }
    }

    private function nouCentreInstructor($centro, $dni)
    {
        $ci = Centro_instructor::where('idInstructor', $dni)->where('idCentro', $centro)->first();
        if (!$ci) {
            $ci = new Centro_instructor();
            $ci->idCentro = $centro;
            $ci->idInstructor = $dni;
            $ci->save();
        }
    }

    public function crea($centro)
    {
        return parent::create();
    }
    
    public function edita($id,$empresa)
    {
        return parent::edit($id);
    }
    
    public function guarda(Request $request, $id,$centro)
    {
        parent::update($request, $id);
        Session::put('pestana',2);
        return redirect()->action('EmpresaController@show', ['id' => Centro::find($centro)->idEmpresa]);
    }
    
    public function almacena(Request $request,$centro)
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
            $this->nouCentreInstructor($centro, $request->dni);
        });
        Session::put('pestana',2);
        return redirect()->action('EmpresaController@show', ['id' => Centro::find($centro)->idEmpresa]);
    }

    public function delete($id,$centro)
    {
        $instructor = Instructor::find($id);
        $instructor->Centros()->detach($centro);
        if (Centro_instructor::where('idInstructor', $id)->count() == 0)
            parent::destroy($id);
        Session::put('pestana',2);
        return redirect()->action('EmpresaController@show', ['id' => Centro::find($centro)->idEmpresa]);
    }
    public function copy($id,$idCentro)
    {
        $instructor = Instructor::findOrFail($id);
        $centro = Centro::findOrFail($idCentro);
        $posibles = [];
        foreach ($centro->Empresa->centros as $centre){
            if (Centro_instructor::where('idCentro',$centre->id)->where('idInstructor',$id)->count()==0)
                $posibles[$centre->id] = $centre->nombre.'('.$centre->direccion.')';
        }
        return view('instructor.copy',compact('instructor','posibles','centro'));
    }
    public function toCopy(Request $request,$id,$idCentro)
    {
        $instructor = Instructor::findOrFail($id);
        $centro = Centro::findOrFail($idCentro);
        $instructor->Centros()->attach($request->centro);
        if ($request->accion == 'mou'){
            $instructor->Centros()->detach($idCentro);
        }
        Session::put('pestana',2);
        return redirect()->action('EmpresaController@show', ['id' => Centro::find($idCentro)->idEmpresa]);
    }
    
    public function pdf($id)
    {
        $instructor = Instructor::findOrFail($id);
        if ($instructor->surnames != ''){
            $fcts = $instructor->Fcts;
            $fecha = $this->ultima_fecha($fcts);
            $secretario = Profesor::find(config('constants.contacto.secretario'));
            $director = Profesor::find(config('constants.contacto.director'));
            $dades = ['date' => FechaString($fecha,'ca'),
                'fecha' => FechaString($fecha,'es'),
                'consideracion' => $secretario->sexo === 'H' ? 'En' : 'Na',
                'secretario' => $secretario->FullName,
                'centro' => config('constants.contacto.nombre'),
                'poblacion' => config('constants.contacto.poblacion'),
                'provincia' => config('constants.contacto.provincia'),
                'director' => $director->FullName,
                'instructor' => $instructor
            ];
            if ($fcts->count()==1)
                $pdf = $this->hazPdf('pdf.fct.instructor', $fcts->first(), $dades);
            else
            {
                $centros = [];
                foreach ($fcts as $fct){
                    if (!in_array($fct->Colaboracion->idCentro, $centros))
                        $centros[] = $fct->Colaboracion->idCentro;
                }
                $pdf = $this->hazPdf('pdf.fct.instructors', $centros, $dades);
            }
            return $pdf->stream();
        }
        else
        {
            Alert::danger("Completa les dades de l'instructor");
            return redirect("/instructor");   
        }
        
    }
    private function ultima_fecha($fcts)
    {
        $posterior = new Date();
        foreach ($fcts as $fct){
            $posterior = FechaPosterior($fct->hasta, $posterior);
        }
        return $posterior;
    }


}
