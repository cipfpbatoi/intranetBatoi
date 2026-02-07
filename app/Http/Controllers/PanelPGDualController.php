<?php

namespace Intranet\Http\Controllers;


use Intranet\Entities\Grupo;
use Illuminate\Support\Facades\Session;
use Intranet\UI\Botones\BotonImg;
use Intranet\Entities\AlumnoFct;

class PanelPGDualController extends BaseController
{

    protected $perfil = 'profesor';
    protected $model = 'Fctdual';
    protected $vista = ['index' => 'FctDual'];
    protected $gridFields = ['id','Nombre','Centro' ,'desde','hasta'];
    
    protected function iniBotones()
    {
        Session::put('redirect', 'PanelPGDualController@indice');
    }
    
    protected function search()
    {
        $grupo = Grupo::findOrFail($this->search);
        $this->titulo = ['quien' => $grupo->nombre ];
        return $grupo->codigo;
    }
    
    

}
