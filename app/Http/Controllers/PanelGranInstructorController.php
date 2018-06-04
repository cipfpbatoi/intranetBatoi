<?php

namespace Intranet\Http\Controllers;

use Intranet\Botones\BotonImg;
use Intranet\Botones\BotonIcon;
use Intranet\Entities\Instructor;
use DB;

class PanelGranInstructorController extends BaseController
{
    
    protected $perfil = 'profesor';
    protected $model = 'Instructor';
    protected $gridFields = ['nombre', 'Nfcts'];
    protected $profile = false;
    
    public function search()
    {
        return Instructor::where('Nfcts','>',1)->get();
    }
     protected function iniBotones()
    {
        //$this->panel->setBotonera();
        $this->panel->setBoton('grid', new BotonImg('fct.delete'));
        $this->panel->setBoton('grid', new BotonImg('fct.edit'));
        $this->panel->setBoton('grid', new BotonImg('fct.show'));
    }
    
}
