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
    
    protected $model = 'Documento';
    protected $gridFields = ['curso', 'descripcion', 'tags', 'ciclo'];
    
    
    protected function iniPestanas($parametres = null)
    {
        $dep = isset(AuthUser()->departamento)?AuthUser()->departamento:AuthUser()->Grupo->first()->departamento;
        $ciclos = Ciclo::select('ciclo')
                 ->where('departamento', $dep)
                ->where('tipo',2)
                ->distinct()
                ->get();

        foreach ($ciclos as $ciclo) {
            $this->panel->setPestana(str_replace([' ', '(', ')', '.'], '', $ciclo->ciclo), true, 'profile.documento', ['ciclo', $ciclo->ciclo]);
        }
    }
    
    public function search()
    {
        $dep = isset(AuthUser()->departamento)?AuthUser()->departamento:AuthUser()->Grupo->first()->departamento;
        $ciclos = hazArray(Ciclo::select('ciclo')
            ->where('departamento', $dep)
            ->where('tipo',2)
            ->distinct()
            ->get(),'ciclo','ciclo');

        return Documento::where('tipoDocumento', 'Proyecto')
                ->whereIn('ciclo',$ciclos)
                ->orderBy('curso','desc')
                ->get();
    }
    protected function iniBotones()
    {
        $this->panel->setBothBoton('documento.show');
    }
    
}
