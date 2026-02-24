<?php

namespace Intranet\Http\Controllers;

use Intranet\Http\Controllers\Core\IntranetController;
use Intranet\Presentation\Crud\ActividadCrudSchema;
use Intranet\UI\Botones\BotonIcon;
use Intranet\UI\Botones\BotonImg;
use Intranet\UI\Botones\BotonBasico;
use Intranet\Entities\Actividad;
use Illuminate\Support\Facades\Gate;
use Jenssegers\Date\Date;


/**
 * Class PanelActividadOrientacionController
 * @package Intranet\Http\Controllers
 */
class PanelActividadOrientacionController extends IntranetController
{

    /**
     * @var string
     */
    protected $perfil = 'profesor';
    /**
     * @var string
     */
    protected $model = 'Actividad';
    /**
     * @var array
     */
    protected $gridFields = ActividadCrudSchema::ORIENTACION_GRID_FIELDS;
    protected $formFields = ActividadCrudSchema::FORM_FIELDS;
    /**
     * @var bool
     */
    protected $profile = false;
    /**
     * @var bool
     */
    protected $modal = false;


    /**
     *
     */
    protected function iniBotones()
    {
        Gate::authorize('viewAny', Actividad::class);

        $this->panel->setBoton('index',new BotonBasico('actividadOrientacion.create',['roles'=>config('roles.rol.orientador')]));
        $this->panel->setBothBoton('actividad.detalle');
        $this->panel->setBothBoton('actividad.edit');
        $this->panel->setBoton('grid', new BotonImg('actividad.delete'));
        $this->panel->setBoton('profile', new BotonIcon('actividad.delete', ['class' => 'btn-danger']));
        $this->panel->setBoton('grid', new BotonImg('actividad.ics', ['img' => 'fa-calendar', 'where' => ['desde', 'posterior', Date::yesterday()]]));
    }


    /**
     * @param null $grupo
     * @return mixed
     */
    public function search($grupo = null)
    {
        Gate::authorize('viewAny', Actividad::class);
        return Actividad::where('extraescolar', 0)->get();
    }

    /**
     * @param null $default
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    protected function createWithDefaultValues($default=[])
    {
        return new Actividad(['extraescolar' => 0,'fueraCentro'=>0,'complementaria'=>0]);
    }
    
    
    
}
