<?php

namespace Intranet\Http\Controllers;

use Intranet\Entities\Horario;
use Intranet\Entities\Modulo_ciclo;
use Intranet\Entities\Modulo_grupo;
use Intranet\Entities\Profesor;
use Intranet\Entities\Programacion;
use Intranet\Botones\BotonIcon;
use Intranet\Botones\BotonPost;
use Intranet\Botones\BotonImg;
use Intranet\Botones\BotonBasico;
use Illuminate\Http\Request;
use Styde\Html\Facades\Alert;
use Illuminate\Support\Facades\Session;
use Intranet\Jobs\SendEmail;

class ProgramacionController extends IntranetController
{

    use traitAutorizar,traitCheckList;

    protected $model = 'Programacion';
    protected $gridFields = ['Xciclo','XModulo', 'curso', 'situacion'];
    protected $vista = ['seguimiento' => 'programacion.seguimiento'];
    protected $modal = false;
    protected $items = 6;
    
    
    protected function search()
    {
        return Programacion::misProgramaciones()
            ->with('Ciclo')
            ->with('Modulo')
            ->get();
    }
    
    //inicializat a init (normalment 1)
    protected function init($id)
    {
        Programacion::putEstado($id,$this->init);
        $prg = Programacion::find($id);
        $prg->Profesor = AuthUser()->dni;
        $prg->save();
        return back();
    }
    
    
    protected function seguimiento($id)
    {
        $elemento = Programacion::findOrFail($id);
        return view($this->chooseView('seguimiento'), compact('elemento'));
    }


    public function avisaFaltaEntrega($id)
    {
        $modulo = Modulo_grupo::find($id);
        foreach ($modulo->profesores() as $profesor){
            $texto = "Et falta per omplir el seguiment de l'avaluacio '" .
                "' del mòdul '$modulo->Xmodulo' del Grup '$modulo->Xgrupo'";
            avisa($profesor['idProfesor'], $texto);
        }
        Alert::info('Aviss enviat');
        return back();
    }

    protected function advise($id){
        $elemento = Modulo_ciclo::findOrFail($id);
        if (isset($elemento->Modulo->codigo)){
            $horario = Horario::where('modulo',$elemento->Modulo->codigo)
                ->first();
            if ($horario){
                avisa($horario->idProfesor, 'Et falta entregar la programacio de '.$elemento->Modulo->vliteral);
                Alert::danger("El professor ".$horario->Mestre->FullName." del mòdul ".$elemento->Modulo->vliteral." ha estat avisat.");
            } else {
                Alert::danger("El mòdul ".$elemento->Modulo->vliteral." no té professor associat.");
            }
        }
        return back();
    }
    
    protected function updateSeguimiento(Request $request,$id){
        $elemento = Programacion::findOrFail($id); 
        $elemento->criterios = $request->criterios;
        $elemento->metodologia = $request->metodologia;
        $elemento->propuestas = $request->propuestas;
        $elemento->save();
        return $this->redirect();
    }


    protected function link($id)
    {
        $elemento = Programacion::findOrFail($id);
        return redirect()->away($elemento->fichero);
    }

    protected function createWithDefaultValues( $default=[]){
        return new Programacion(['curso'=>Curso()]);
    }

    
    
    
    protected function iniBotones()
    {
        $this->panel->setBotonera();
        $this->panel->setBoton('grid', new BotonImg('programacion.link', ['img' => 'fa-link']));
        $this->panel->setBoton('grid', new BotonImg('programacion.init', ['where' => ['estado', '==', 0]]));
        $this->panel->setBoton('grid', new BotonImg('programacion.seguimiento', ['img' => 'fa-binoculars','orWhere' => ['estado', '==', 0,'estado', '==', 3]]));

    }
    
    
    
}
