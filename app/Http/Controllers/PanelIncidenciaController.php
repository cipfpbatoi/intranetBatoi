<?php
namespace Intranet\Http\Controllers;

use Intranet\Entities\Incidencia;
use Intranet\Botones\BotonImg;
use Intranet\Botones\BotonIcon;
use Intranet\Botones\BotonBasico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Intranet\Entities\OrdenTrabajo;

class PanelIncidenciaController extends BaseController
{

    use traitPanel;
    
    protected $perfil = 'profesor';
    protected $model = 'Incidencia';
    protected $gridFields = ['Xestado', 'DesCurta', 'espacio', 'XCreador', 
        'XResponsable', 'Xtipo', 'fecha','Observaciones','solucion'];
    protected $orden = 'fecha';
    protected $parametresVista = ['modal' => ['explicacion','aviso']];
    
    public function index()
    {
        $this->panel->setPestana('NoAsig',0,'profile.incidencia',['estado',1,'responsable',''],null,null,$this->parametresVista);
        $condicion = ['responsable', AuthUser()->dni];
        $activa = Session::get('pestana') ? Session::get('pestana') : 0;
        $todos = $this->search();
        
        foreach (config('modelos.'.$this->model.'.estados') as $key => $estado) {
            $this->panel->setPestana($estado, $key == $activa ? true : false, "profile." .
                strtolower($this->model),
                isset($condicion)?array_merge(['estado',$key],$condicion):['estado',$key],null,null,$this->parametresVista);
        }
        $this->iniBotones();
        Session::put('redirect','PanelIncidenciaController@index');
        return $this->grid($todos);
    }
   
    protected function iniBotones()
    {
        $this->panel->setBoton('profile', new BotonIcon("$this->model.authorize", ['class' => 'btn-success authorize', 'where' => ['estado', '==', '1','orden','==',null]], 'mantenimiento'));
        $this->panel->setBoton('profile', new BotonIcon("$this->model.unauthorize", ['class' => 'btn-danger unauthorize', 'where' => ['estado', '==', '2','orden','==',null]], 'mantenimiento'));
        $this->panel->setBoton('profile', new BotonIcon("$this->model.refuse", ['class' => 'btn-danger refuse', 'where' => ['estado', '==', '1','orden','==',null]], 'mantenimiento'));
        $this->panel->setBoton('profile', new BotonIcon("$this->model.resolve", ['class' => 'btn-success resolve', 'where' => ['estado', '==', '2','orden','==',null]], 'mantenimiento'));
        $this->panel->setBoton('grid', new BotonImg('incidencia.edit', ['where' => ['estado', '<', '2']]));
        $this->panel->setBoton('grid', new BotonImg('incidencia.delete', ['where' => ['estado', '<', '2']]));
        $this->panel->setBoton('profile', new BotonIcon("$this->model.unauthorize", ['class' => 'btn-danger unauthorize', 'where' => ['estado', '==', '3']], 'mantenimiento'));
        $this->panel->setBothBoton('incidencia.orden', ['where' => ['orden', '==',null,'estado','<','3']],'mantenimiento');
        
    }
}
