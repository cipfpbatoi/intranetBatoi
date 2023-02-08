<?php

namespace Intranet\Http\Controllers;


use Intranet\Entities\Grupo;
use Illuminate\Support\Facades\Session;
use Intranet\Botones\BotonImg;
use Intranet\Entities\AlumnoFct;

class PanelPG0301Controller extends BaseController
{

    protected $perfil = 'profesor';
    protected $model = 'Fctcap';
    protected $vista = ['index' => 'FctCap'];
    protected $gridFields = ['id','Nombre','Centro' ,'desde','hasta'];
    
    protected function iniBotones()
    {
        Session::put('redirect', 'PanelPG0301Controller@indice');
    }
    
    protected function search()
    {
        $grupo = Grupo::findOrFail($this->search);
        $this->titulo = ['quien' => $grupo->nombre ];
        return $grupo->codigo;
    }
    
    

}
