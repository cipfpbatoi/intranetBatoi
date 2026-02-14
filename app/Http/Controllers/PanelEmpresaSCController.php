<?php

namespace Intranet\Http\Controllers;

use Intranet\Http\Controllers\Core\BaseController;

use Intranet\Entities\Empresa;
use Intranet\UI\Botones\BotonBasico;
use Intranet\UI\Botones\BotonImg;


/**
 * Class PanelEmpresaSCController
 * @package Intranet\Http\Controllers
 */
class PanelEmpresaSCController extends BaseController
{
    const ROLES_ROL_TUTOR = 'roles.rol.tutor';
    const ROLES_ROL_DUAL = 'roles.rol.dual';

    /**
     * @var string
     */
    protected $perfil = 'profesor';
    /**
     * @var string
     */
    protected $model = 'Empresa';
    /**
     * @var array
     */
    protected $gridFields = ['nombre', 'direccion', 'localidad', 'telefono', 'email', 'actividad','cicles','concierto'];
    /**
     * @var array
     */
    protected $vista = ['index' => 'empresa.indexSC'];


    /**
     * @return mixed
     */
    public function search()
    {
        return Empresa::whereNull('concierto')->get();
    }

    /**
     *
     */
    protected function iniBotones()
    {
       $this->panel->setBoton(
           'index',
           new BotonBasico(
               "empresa.create",
               ['roles' => [config(self::ROLES_ROL_TUTOR),config(self::ROLES_ROL_DUAL)]]
           )
       );
       $this->panel->setBoton(
           'grid',
           new BotonImg(
               'empresa.detalle',
               ['roles' => [config(self::ROLES_ROL_TUTOR),config(self::ROLES_ROL_DUAL)]]
           )
       );
       $this->panel->setBoton(
           'grid',
           new BotonImg(
               'empresa.delete',
               ['roles' => [config(self::ROLES_ROL_TUTOR),config(self::ROLES_ROL_DUAL)]]
           )
       );
    }
}
