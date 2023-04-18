<?php

namespace Intranet\Http\Controllers;

use Intranet\Entities\Reunion;
use Intranet\Botones\BotonImg;
use Intranet\Botones\Panel;
use Illuminate\Support\Facades\Session;

class PanelInfDptoController extends BaseController
{


    protected $perfil = 'profesor';
    protected $model = 'InfDepartamento';
    protected $gridFields = ['departamento', 'avaluacio'];
    
    
    protected function search()
    {
        return Reunion::Tipo(10)->with('Departament')->get();
    }

    protected function iniPestanas($parametres = null)
    {
        $this->panel->setPestana('profile', true, null, null, null, true);
    }

}
