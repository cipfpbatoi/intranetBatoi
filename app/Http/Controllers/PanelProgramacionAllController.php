<?php

namespace Intranet\Http\Controllers;

use Intranet\Entities\Programacion;
use Intranet\Botones\BotonImg;
use Illuminate\Support\Facades\Session;

class PanelProgramacionAllController extends BaseController
{

   
    protected $model = 'Programacion';
    protected $gridFields = ['XCiclo','XModulo', 'Xdepartamento'];
    protected $redirect = 'PanelProgramacionAllController@index';
    protected $parametresVista = [];
    
    public function search(){
        return Programacion::where('estado',3)
            ->with('Departament')
            ->with('Ciclo')
            ->with('Modulo')
            ->get();
    }
    protected function iniPestanas($parametres = null)
    {
        // buid
    }

    protected function iniBotones()
    {
        $this->panel->setBotonera();
        if (config('variables.programaciones.fichero')){
            $this->panel->setBoton('grid', new BotonImg('programacion.document', ['img' => 'fa-eye','where' => ['fichero','isNNull','']]));
            $this->panel->setBoton('grid', new BotonImg('programacion.anexo', ['img' => 'fa-plus','where' => ['anexos', '>', 0]]));
        }
        else {
            $this->panel->setBoton('grid', new BotonImg('programacion.link', ['img' => 'fa-link']));
        }
        $this->panel->setBoton('grid', new BotonImg('programacion.edit',['roles' => config('roles.rol.administrador')]));
    }
}
