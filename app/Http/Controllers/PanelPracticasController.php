<?php

namespace Intranet\Http\Controllers;

use Intranet\Botones\BotonImg;
use Intranet\Entities\Grupo;

class PanelPracticasController extends BaseController
{

    protected $perfil = 'profesor';
    protected $model = 'Grupo';
    protected $gridFields = ['nombre','Matriculados','Resfct','Respro', 'Acta', 'Calidad','Xtutor'];
    
    protected function iniBotones()
    {
        $this->panel->setBoton('grid',new BotonImg('direccion.acta',['img' => 'fa-file-word-o','roles' => config('constants.rol.direccion'),'where' => ['acta_pendiente','==','1']]));
        
    }
    protected function search(){
        return Grupo::where('curso',2)->get();
    }

}
