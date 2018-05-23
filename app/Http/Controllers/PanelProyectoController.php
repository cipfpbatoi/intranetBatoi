<?php

namespace Intranet\Http\Controllers;

use Intranet\Botones\BotonImg;
use Intranet\Botones\BotonIcon;
use Intranet\Entities\Documento;
use Intranet\Entities\Ciclo;
use Illuminate\Support\Facades\Session;
use Styde\Html\Facades\Alert;


class PanelProyectoController extends BaseController
{
    
    protected $perfil = 'profesor';
    protected $model = 'Documento';
    protected $gridFields = ['curso', 'descripcion', 'tags', 'ciclo'];
    
    public function proyecto()
    {
        
        //$this->panel->setTitulo($this->titulo);
        $this->panel->setElementos($todos);
        
    }
    public function index()
    {
        Session::forget('redirect'); 
        $dep = isset(AuthUser()->departamento)?AuthUser()->departamento:AuthUser()->Grupo->first()->departamento;
        $grupos = Ciclo::select('ciclo')
                ->where('departamento', $dep)
                ->where('tipo',2)
                ->distinct()
                ->get();
        if ($grupos->count()){
            foreach ($grupos as $grupo) 
                $this->panel->setPestana(str_replace([' ', '(', ')'], '', $grupo->ciclo), true, 'profile.documento', ['ciclo', $grupo->ciclo]);
            $this->iniBotones();
            $this->panel->setElementos($this->search());
            return view('documento.grupo', ['panel' => $this->panel]);
        } else {
            Alert::danger(trans("messages.generic.noproyecto"));
            return redirect()->route('home');
        }
    }
    
    public function search()
    {
        return Documento::where('tipoDocumento', 'Proyecto')
                ->orderBy('curso')
                ->get();
    }
     protected function iniBotones()
    {
        $this->panel->setBothBoton('documento.show');
    }
    
}
