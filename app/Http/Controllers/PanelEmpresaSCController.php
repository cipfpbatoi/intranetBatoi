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
       $this->panel->setBotonera(['create'], ['detalle','delete']); 
    }
}
