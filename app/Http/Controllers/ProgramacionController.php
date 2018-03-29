<?php

namespace Intranet\Http\Controllers;

use Intranet\Entities\Programacion;
use Intranet\Botones\BotonIcon;
use Intranet\Botones\BotonPost;
use Intranet\Botones\BotonImg;
use Intranet\Botones\BotonBasico;
use Illuminate\Http\Request;
use Styde\Html\Facades\Alert;
use Illuminate\Support\Facades\Session;

class ProgramacionController extends IntranetController
{

    use traitAutorizar;

    protected $model = 'Programacion';
    protected $gridFields = ['idModulo','XModulo', 'ciclo', 'hasta', 'anexos', 'situacion'];
    protected $vista = ['seguimiento' => 'programacion.seguimiento'];
    protected $modal = true;
    
    
    protected function search()
    {
        return Programacion::misProgramaciones()
                ->get();
    }
    
    protected function seguimiento($id)
    {
        $elemento = $this->class::findOrFail($id);
        return view($this->chooseView('seguimiento'), compact('elemento'));
    }
    
    protected function updateSeguimiento(Request $request,$id){
        $elemento = $this->class::findOrFail($id); 
        $elemento->criterios = $request->criterios;
        $elemento->metodologia = $request->metodologia;
        $elemento->propuestas = $request->propuestas;
        $elemento->save();
        return $this->redirect();
    }

    protected function anexo($id)
    {
        $elemento = Programacion::findOrFail($id);
        return view('programacion.anexo', compact('elemento'));
    }

    protected function storeanexo(Request $request, $id)
    {
        if ($request->hasFile('Anexo'))
            if (($request->file('Anexo')->isValid()) && (($extension = $request->file('Anexo')->getClientOriginalExtension()) == 'pdf')) {
                $elemento = Programacion::findOrFail($id);
                $directorio = 'gestor/' . Curso() . '/' . $this->model;
                $nom = $elemento->nomFichero() . '_an' . ++$elemento->anexos . '.' . $extension;
                $request->file('Anexo')->storeAs($directorio,$nom);
                //$request->file('Anexo')->move(storage_path('/app/'.$directorio), $nom);
                $elemento->save();
                return back();
            }
        Alert::danger(trans('messages.generic.invalidFormat'));
        return back();
    }

    protected function deleteanexo($id)
    {
        $elemento = Programacion::findOrFail($id);
        $elemento->anexos = $elemento->anexos > 0 ? $elemento->anexos - 1 : 0;
        $elemento->save();
        return back();
    }
    protected function veranexo($id,$an){
        $elemento = $this->class::findOrFail($id);
        $fichero = 'gestor/' . Curso() . '/' . $this->model.'/'.$elemento->nomFichero()."_an".$an.".pdf";
        return response()->file(storage_path('/app/'.$fichero));
    }
    
    
    protected function iniBotones()
    {
        $this->panel->setBotonera();
        $this->panel->setBoton('grid', new BotonImg('programacion.document', ['img' => 'fa-eye','where' => ['fichero','isNNull','']]));
        $this->panel->setBoton('grid', new BotonImg('programacion.anexo', ['img' => 'fa-plus','where' => ['estado','>','2','anexos', '>', 0]]));
        $this->panel->setBoton('index', new BotonBasico('programacion.create', ['roles' => [config('constants.rol.profesor')]]));
        $this->panel->setBoton('grid', new BotonImg('programacion.anexo', ['img' => 'fa-plus','where' => ['estado', '<', 3]]));
        $this->panel->setBoton('grid', new BotonImg('programacion.edit', ['where' => ['estado', '<', 2]]));
        $this->panel->setBoton('grid', new BotonImg('programacion.delete', ['where' => ['estado', '<', 2]]));
        $this->panel->setBoton('grid', new BotonImg('programacion.init', ['where' => ['estado', '==', 0,'fichero','isNNull','']]));
        $this->panel->setBoton('grid', new BotonImg('programacion.seguimiento', ['img' => 'fa-binoculars','where' => ['estado', '==', 3]]));
    }
    
}
