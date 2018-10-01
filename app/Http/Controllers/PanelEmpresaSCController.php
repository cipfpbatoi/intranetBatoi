<?php

namespace Intranet\Http\Controllers;

use Intranet\Entities\Empresa;




class PanelEmpresaSCController extends BaseController
{

    protected $perfil = 'profesor';
    protected $model = 'Empresa';
    protected $gridFields = ['nombre', 'direccion', 'localidad', 'telefono', 'email', 'actividad'];
    protected $vista = ['index' => 'empresa.indexSC'];

    
    public function search(){
        return Empresa::whereNull('concierto')->get();
    }
    protected function iniBotones()
    {
       $this->panel->setBoton('index', new BotonBasico("empresa.create",['roles' => [config('roles.rol.practicas'),config('roles.rol.dual')]]));
       $this->panel->setBoton('grid', new BotonImg('empresa.detalle',['roles' => [config('roles.rol.practicas'),config('roles.rol.dual')]]));
       $this->panel->setBoton('grid', new BotonImg('empresa.delete',['roles' => [config('roles.rol.practicas'),config('roles.rol.dual')]]));
    }
}
