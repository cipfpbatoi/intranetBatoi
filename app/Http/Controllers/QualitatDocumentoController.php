<?php

namespace Intranet\Http\Controllers;

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
class QualitatDocumentoController extends BaseController
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
        Session::put('redirect','QualitatDocumentoController@index');
        $this->iniBotones();
        return $this->grid($this->search());
    }

    /**
     *
     */
    protected function iniBotones()
    {
        $this->panel->setBoton('index', new BotonBasico('documento.create', ['roles' => config('roles.rol.qualitat')]));
        $this->panel->setBothBoton('documento.show', ['where' => ['link','==',1]]);
        $this->panel->setBoton('grid', new BotonImg('documento.edit'));
        $this->panel->setBoton('grid', new BotonImg('documento.delete'));
    }

    /**
     * @return mixed
     */
    public function search()
    {
        return Documento::where('tipoDocumento','Millora')->whereNull('idDocumento')->get();
    }

}
