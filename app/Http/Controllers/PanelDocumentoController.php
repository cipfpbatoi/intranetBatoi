<?php

namespace Intranet\Http\Controllers;

use Intranet\Botones\BotonIcon;
use Intranet\Botones\BotonImg;
use Intranet\Botones\BotonBasico;
use Intranet\Entities\TipoDocumento;
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
        $this->panel->setBoton('index', new BotonBasico('documento.create', ['roles' => config('roles.rol.direccion')]));
        $this->panel->setBothBoton('documento.show', ['where' => ['link','==',1]]);
        $this->panel->setBoton('grid', new BotonImg('documento.edit'));
        $this->panel->setBoton('grid', new BotonImg('documento.delete'));
    }

    /**
     * @return mixed
     */
    public function search()
    {
        return Documento::whereIn('rol', RolesUser(AuthUser()->rol))->whereIn('tipoDocumento',TipoDocumento::allDocuments())->whereNull('idDocumento')->get();
    }

}
