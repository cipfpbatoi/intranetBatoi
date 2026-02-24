<?php

namespace Intranet\Http\Controllers;

use Intranet\Application\Empresa\EmpresaService;
use Intranet\Http\Controllers\Core\BaseController;

use Intranet\UI\Botones\BotonBasico;
use Intranet\UI\Botones\BotonImg;
use Intranet\Entities\Empresa;
use Illuminate\Support\Facades\Gate;


/**
 * Class PanelEmpresaSCController
 * @package Intranet\Http\Controllers
 */
class PanelEmpresaSCController extends BaseController
{
    const ROLES_ROL_TUTOR = 'roles.rol.tutor';
    const ROLES_ROL_DUAL = 'roles.rol.dual';
    private ?EmpresaService $empresaService = null;

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

    public function __construct(?EmpresaService $empresaService = null)
    {
        parent::__construct();
        $this->empresaService = $empresaService;
    }

    protected function empreses(): EmpresaService
    {
        if ($this->empresaService === null) {
            $this->empresaService = app(EmpresaService::class);
        }

        return $this->empresaService;
    }

    /**
     * @return mixed
     */
    public function search()
    {
        Gate::authorize('viewAny', Empresa::class);
        return $this->empreses()->socialConcertList();
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
