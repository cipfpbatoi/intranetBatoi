<?php

namespace Intranet\Http\Controllers;

use Intranet\Http\Controllers\Core\BaseController;

use Intranet\Entities\Reunion;

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
