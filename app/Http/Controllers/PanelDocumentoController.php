<?php

namespace Intranet\Http\Controllers;

use Intranet\Http\Controllers\Core\BaseController;

use Intranet\UI\Botones\BotonIcon;
use Intranet\UI\Botones\BotonImg;
use Intranet\UI\Botones\BotonBasico;
use Intranet\Services\Document\TipoDocumentoService;
use Intranet\Entities\Documento;
use Illuminate\Support\Facades\Session;


/**
 * Class PanelDocumentoController
 * @package Intranet\Http\Controllers
 */
class PanelDocumentoController extends BaseController
{


    /**
     * @var string
     */
    protected $perfil = 'profesor';
    /**
     * @var string
     */
    protected $model = 'Documento';
    /**
     * @var array
     */
    protected $gridFields = ['tipoDocumento', 'descripcion', 'created_at'];


    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function index()
    {
        Session::put('redirect','PanelDocumentoController@index');
        $this->iniBotones();
        return $this->grid($this->search());
    }

    /**
     *
     */
    protected function iniBotones()
    {
        $this->panel->setBoton('index',
            new BotonBasico('documento.create', ['roles' => config('roles.rol.direccion')])
        );
        $this->panel->setBothBoton('documento.show', ['where' => ['link','==',1]]);
        $this->panel->setBoton('grid', new BotonImg('documento.edit'));
        $this->panel->setBoton('grid', new BotonImg('documento.delete'));
    }

    /**
     * @return mixed
     */
    public function search()
    {
        return Documento::
            whereIn('rol', RolesUser(AuthUser()->rol))->
            whereIn('tipoDocumento',TipoDocumentoService::allDocuments())->
            whereNull('idDocumento')->get();
    }

}
