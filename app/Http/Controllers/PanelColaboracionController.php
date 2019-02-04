<?php

namespace Intranet\Http\Controllers;

use Intranet\Botones\BotonImg;
use Intranet\Entities\Colaboracion;
use Illuminate\Support\Facades\Session;
use Mail;
use Intranet\Mail\DocumentRequest;


class PanelColaboracionController extends IntranetController
{
    
    protected $gridFields = ['Empresa','concierto','Localidad','puestos','Xcolabora','contacto', 'telefono','email'];
    protected $perfil = 'profesor';
    protected $model = 'miscolaboraciones';
    
    
    protected function iniBotones()
    {
        $this->panel->setBotonera(['inicia','contacto','documentacion']);
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
    public function documentacion(){
        $colaboraciones = Colaboracion::MiColaboracion()->where('colabora',1)->get();
        foreach ($colaboraciones as $colaboracion){
            if (!$colaboracion->concierto){
                Mail::to('ignasi.gomis.mullor@gmail.com', AuthUser()->ShortName)->send(new DocumentRequest($colaboracion, AuthUser()->email));
       
                //Mail::to($colaboracion->email, AuthUser()->ShortName)->send(new DocumentRequest($colaboracion, AuthUser()->email));
            }
        }
    }
}
