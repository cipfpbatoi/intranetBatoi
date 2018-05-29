<?php

namespace Intranet\Http\Controllers;

use Intranet\Entities\Programacion;
use Intranet\Botones\BotonImg;
use Illuminate\Support\Facades\Session;

class PanelProgramacionAllController extends BaseController
{

   
    protected $model = 'Programacion';
    protected $gridFields = ['idModulo','descripcion','XModulo', 'ciclo','Xdepartamento', 'Xnombre'];
    
    public function index()
    {
        Session::forget('redirect'); //buida variable de sessió redirect ja que sols se utiliza en cas de direccio
        Session::put('redirect','PanelProgramacionAllController@index');
        $this->iniBotones();
        return $this->grid(Programacion::Where('estado', '=', '3')->get());
    }
    protected function iniBotones()
    {
        $this->panel->setBotonera();
        $this->panel->setBoton('grid', new BotonImg('programacion.document', ['img' => 'fa-eye','orWhere' => ['fichero','isNNull','',config('constants.programaciones.fichero'),'==','0']]));
        $this->panel->setBoton('grid', new BotonImg('programacion.anexo', ['img' => 'fa-plus','where' => ['anexos', '>', 0]]));
        $this->panel->setBoton('grid', new BotonImg('programacion.edit',['roles' => config('constants.rol.administracion')]));
    }
}
