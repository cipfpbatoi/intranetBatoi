<?php

namespace Intranet\Http\Controllers;

use Intranet\Entities\Programacion;
use Intranet\Botones\BotonIcon;
use Intranet\Botones\BotonPost;


class PanelProgramacionController extends BaseController
{

    use traitPanel,traitCheckList;

    protected $model = 'Programacion';
    protected $gridFields = ['idModulo','XModulo', 'ciclo', 'Xnombre', 'situacion'];
    protected $items = 6;
    protected $vista = ['seguimiento' => 'programacion.seguimiento'];
    protected $modal = true;
    
   
    protected function search()
    {
        return Programacion::where('estado', '>', '0')
                ->Departamento()
                ->get();
    }

    protected function iniBotones()
    {
        $this->panel->setBoton('profile', new BotonPost("$this->model.checklist", ['class' => 'btn-primary checklist', 'where' => ['estado', '==', '1']], true));
        $this->panel->setBoton('profile', new BotonIcon("$this->model.resolve", ['class' => 'btn-success resolve', 'where' => ['estado', '==', '2']], true));
        $this->panel->setBoton('profile', new BotonIcon("$this->model.unauthorize", ['class' => 'btn-danger unauthorize', 'where' => ['estado', '>=', '2']], true));
    }
}
