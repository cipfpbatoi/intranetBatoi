<?php

namespace Intranet\Http\Controllers;

use Intranet\Botones\BotonImg;
use Intranet\Entities\Colaboracion;
use Illuminate\Support\Facades\Session;


class PanelColaboracionController extends IntranetController
{
    
    protected $gridFields = ['Empresa','concierto','puestos','Xcolabora','contacto', 'telefono',];
    protected $perfil = 'profesor';
    protected $model = 'miscolaboraciones';
    
    
    protected function iniBotones()
    {
        $this->panel->setBotonera(['inicia']);
        $this->panel->setBoton('grid', new BotonImg('miscolaboraciones.colabora.2', ['roles' => config('roles.rol.practicas'),'img'=>'fa-hand-o-down','where' => ['colabora', '!=', '2']]));
        $this->panel->setBoton('grid', new BotonImg('miscolaboraciones.colabora.1', ['roles' => config('roles.rol.practicas'),'img'=>'fa-hand-o-up','where' => ['colabora', '!=', '1']]));
        $this->panel->setBoton('grid', new BotonImg('miscolaboraciones.colabora.0', ['roles' => config('roles.rol.practicas'),'img'=>'fa-question','where' => ['colabora', '!=', '0']]));
        $this->panel->setBoton('grid', new BotonImg('miscolaboraciones.colabora.3', ['roles' => config('roles.rol.practicas'),'img'=>'fa-cloud','where' => ['colabora', '==', '0']]));
        $this->panel->setBoton('grid', new BotonImg('colaboracion.show',['roles' => [config('roles.rol.practicas'),config('roles.rol.dual')]]));
  
        Session::put('redirect', 'PanelColaboracionController@index');
    }
    public function search(){
        return Colaboracion::MiColaboracion()->get();
    }
    
    public function colabora($id,$tipo){
        $colaboracion = Colaboracion::find($id);
        $colaboracion->colabora = $tipo;
        $colaboracion->save();
        return $this->redirect();
    }
    public function inicia(){
        Colaboracion::MiColaboracion()->update(['colabora' => 0]);
        return $this->redirect();
    }
}
