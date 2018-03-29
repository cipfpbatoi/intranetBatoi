<?php

namespace Intranet\Http\Controllers;

use Intranet\Entities\Programacion;
use Intranet\Botones\BotonImg;
use Illuminate\Support\Facades\Session;

class PanelProgramacionAllController extends BaseController
{

   
    protected $model = 'Programacion';
    protected $gridFields = ['idModulo','XModulo', 'ciclo','Xdepartamento', 'Xnombre'];
    
    public function index()
    {
        Session::forget('redirect'); //buida variable de sessió redirect ja que sols se utiliza en cas de direccio
        $this->iniBotones();
        return $this->grid(Programacion::Where('estado', '=', '3')->get());
    }
    protected function iniBotones()
    {
        $this->panel->setBotonera();
        $this->panel->setBoton('grid', new BotonImg('programacion.document', ['img' => 'fa-eye','where' => ['fichero','isNNull','']]));
        $this->panel->setBoton('grid', new BotonImg('programacion.anexo', ['img' => 'fa-plus','where' => ['estado','>','2','anexos', '>', 0]]));
    }
}
