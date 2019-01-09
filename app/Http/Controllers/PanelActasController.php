<?php

namespace Intranet\Http\Controllers;

use Intranet\Botones\BotonBasico;
use Intranet\Entities\Grupo;
use Intranet\Entities\AlumnoFctAval;
use Mail;
use Intranet\Mail\AvalFct;

class PanelActasController extends BaseController
{

    protected $perfil = 'profesor';
    protected $model = 'AlumnoFctAval';
    protected $gridFields = ['Nombre', 'hasta', 'horas', 'qualificacio', 'projecte'];
    protected $vista = ['index' => 'intranet.list'] ;  
    
    
    protected function iniBotones()
    {
        if (Grupo::findOrFail($this->search)->acta_pendiente)
            $this->panel->setBoton('index', new BotonBasico("direccion.$this->search.finActa",['text'=>'acta']));
    }
    
    protected function search(){
        $grupo = Grupo::findOrFail($this->search);
        $this->titulo = ['quien' => $grupo->nombre ];
        if ($grupo->acta_pendiente)
            return AlumnoFctAval::Grupo($grupo)->Pendiente()->get();
        else 
            return [];
    }
    
    public function finActa($idGrupo){
        $grupo = Grupo::findOrFail($idGrupo);
        $fcts = AlumnoFctAval::Grupo($grupo)->Pendiente()->get();
        $empresas = [];
        foreach ($fcts as $fct){
            $fct->actas = 2;
            $fct->save();
            if (isset($fct->Fct->Colaboracion->Centro->nombre))
                $empresas[$fct->Fct->Colaboracion->Centro->nombre] = 
                        isset($empresas[$fct->Fct->Colaboracion->Centro->nombre])
                        ?$empresas[$fct->Fct->Colaboracion->Centro->nombre]." , ".$fct->Alumno->FullName
                        :$fct->Alumno->FullName;
        }
        $grupo->acta_pendiente = 0;
        $grupo->save();
        avisa($grupo->tutor, "Ja pots passar a arreplegar l'acta del grup $grupo->nombre", "#");
        Mail::to($grupo->Tutor->email, 'Intranet Batoi')->send(new AvalFct($empresas,'tutor'));
        return back();
    }
    
}
