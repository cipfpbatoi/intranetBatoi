<?php

namespace Intranet\Http\Controllers;

use Intranet\Botones\BotonImg;
use Intranet\Entities\Grupo;

class PanelPracticasController extends BaseController
{

    protected $perfil = 'profesor';
    protected $model = 'Grupo';
    protected $gridFields = ['nombre','Matriculados','Resfct','Exentos','Respro', 'Resempresa','Acta', 'Calidad','Xtutor'];
    
    protected function iniBotones()
    {
        $this->panel->setBoton('grid',new BotonImg('direccion.acta',['img' => 'fa-file-word-o','roles' => config('roles.rol.direccion'),'where' => ['acta_pendiente','==','1']]));
        $this->panel->setBoton('grid',new BotonImg('direccion.acta',['img' => 'fa-file','roles' => config('roles.rol.direccion'),'where' => ['acta_pendiente','!=','1']]));
        
    }
    protected function search(){
        return Grupo::where('curso',2)->get();
    }

}
