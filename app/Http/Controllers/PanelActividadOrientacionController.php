<?php

namespace Intranet\Http\Controllers;
use Intranet\Http\Controllers\ActividadController;
use Intranet\Botones\BotonIcon;
use Intranet\Botones\BotonImg;
use Intranet\Botones\BotonBasico;
use Intranet\Entities\Actividad;
use Jenssegers\Date\Date;


class PanelActividadOrientacionController extends IntranetController
{
    
    protected $perfil = 'profesor';
    protected $model = 'Actividad';
    protected $gridFields = ['name', 'desde', 'hasta'];
    protected $profile = false;
    protected $modal = false;
    
   
    protected function iniBotones()
    {

        $this->panel->setBoton('index',new BotonBasico('actividadOrientacion.create',['roles'=>config('roles.rol.orientador')]));
        $this->panel->setBothBoton('actividad.detalle');
        $this->panel->setBothBoton('actividad.edit');
        $this->panel->setBoton('grid', new BotonImg('actividad.delete'));
        $this->panel->setBoton('profile', new BotonIcon('actividad.delete', ['class' => 'btn-danger']));
        $this->panel->setBoton('grid', new BotonImg('actividad.ics', ['img' => 'fa-calendar', 'where' => ['desde', 'posterior', Date::yesterday()]]));
    }
    
    
    public function search($grupo = null)
    {
        return Actividad::where('extraescolar', 0)->get();
    }
    
    public function create($default = null)
    {
        return parent::create(['extraescolar' => 0]);
    }
    
    
    
}
