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
    
    
    protected function iniPestanas($parametres = null)
    {
        $dep = isset(AuthUser()->departamento)?AuthUser()->departamento:AuthUser()->Grupo->first()->departamento;
        $grupos = Ciclo::select('ciclo')
                ->where('departamento', $dep)
                ->where('tipo',2)
                ->distinct()
                ->get();
        foreach ($grupos as $grupo) 
            $this->panel->setPestana(str_replace([' ', '(', ')'], '', $grupo->ciclo), true, 'profile.documento', ['ciclo', $grupo->ciclo]);
      
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
