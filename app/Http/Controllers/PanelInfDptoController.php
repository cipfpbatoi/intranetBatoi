<?php

namespace Intranet\Http\Controllers;
use Intranet\Entities\Reunion;
use Intranet\Botones\BotonImg;
use Intranet\Botones\Panel;
use Illuminate\Support\Facades\Session;

class PanelInfDptoController extends BaseController
{

    //use traitPanel;
    
    protected $perfil = 'profesor';
    protected $model = 'InfDepartamento';
    protected $gridFields = ['departamento', 'avaluacio'];
    
    protected function search()
    {
        return Reunion::Tipo(10)->get();    
    }
    
    public function index()
    {
        Session::forget('redirect'); //buida variable de sessió redirect ja que sols se utiliza en cas de direccio
        $this->panel = new Panel($this->model, null, null, false);
        $this->panel->setPestana('profile', true);
        return $this->grid(Reunion::Tipo(10)->get());
    }

}
