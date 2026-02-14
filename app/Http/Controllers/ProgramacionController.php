<?php

namespace Intranet\Http\Controllers;

use Intranet\Http\Controllers\Core\IntranetController;

use Illuminate\Http\Request;
use Intranet\UI\Botones\BotonImg;
use Intranet\Entities\Horario;
use Intranet\Entities\Modulo_ciclo;
use Intranet\Entities\Modulo_grupo;
use Intranet\Entities\Programacion;
use Intranet\Http\Traits\Autorizacion;
use Intranet\Services\General\StateService;
use Styde\Html\Facades\Alert;

class ProgramacionController extends IntranetController
{

    use Autorizacion;

    protected $model = 'Programacion';
    protected $gridFields = ['Xciclo','XModulo', 'situacion'];
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
        $prg = Programacion::find($id);
        $staSrv = new StateService($prg);
        $staSrv->putEstado($this->init);
        $prg->Profesor = AuthUser()->dni;
        $prg->save();
        return back();
    }
    
    
    protected function seguimiento($id)
    {
        $elemento = Programacion::findOrFail($id);
        return view('programacion.seguimiento', compact('elemento'));
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

    protected function advise($id)
    {
        $elemento = Modulo_ciclo::findOrFail($id);
        if (isset($elemento->Modulo->codigo)){
            $horario = Horario::where('modulo', $elemento->Modulo->codigo)
                ->first();
            if ($horario) {
                avisa($horario->idProfesor, 'Et falta entregar la programacio de '.$elemento->Modulo->vliteral);
                Alert::danger("El professor ".$horario->Mestre->FullName." del mòdul ".$elemento->Modulo->vliteral." ha estat avisat.");
            } else {
                Alert::danger("El mòdul ".$elemento->Modulo->vliteral." no té professor associat.");
            }
        }
        return back();
    }
    
    protected function updateSeguimiento(Request $request, $id){
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
    
    
    protected function iniBotones()
    {
        $this->panel->setBotonera();
        $this->panel->setBoton('grid', new BotonImg('programacion.link', ['img' => 'fa-link']));
        $this->panel->setBoton(
            'grid',
            new BotonImg(
                'programacion.seguimiento',
                ['img' => 'fa-binoculars','orWhere' => ['estado', '==', 0,'estado', '==', 3]]
            )
        );

    }
    
    
    
}
