<?php

namespace Intranet\Http\Controllers;

use Intranet\Botones\BotonIcon;
use Intranet\Botones\BotonImg;
use Intranet\Botones\BotonBasico;
use Intranet\Entities\TipoDocumento;
use Intranet\Entities\Documento;
use Illuminate\Support\Facades\Session;


class PanelDocumentoController extends BaseController
{

   
    
    protected $perfil = 'profesor';
    protected $model = 'Documento';
    protected $gridFields = ['tipoDocumento', 'descripcion', 'created_at'];
    
    
    public function index()
    {
        Session::put('redirect','PanelDocumentoController@index');
        $this->iniBotones();
        return $this->grid($this->search());
    }
    protected function iniBotones()
    {
        $this->panel->setBoton('index', new BotonBasico('documento.create', ['roles' => config('constants.rol.direccion')]));
        $this->panel->setBothBoton('documento.show', ['where' => ['link','==',1]]);
        $this->panel->setBoton('grid', new BotonImg('documento.edit'));
        $this->panel->setBoton('grid', new BotonImg('documento.delete'));
    }
    
    public function search()
    {
        return Documento::whereIn('rol', RolesUser(AuthUser()->rol))->whereIn('tipoDocumento',TipoDocumento::allDocuments())->whereNull('idDocumento')->get();
    }

}
