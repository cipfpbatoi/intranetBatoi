<?php

namespace Intranet\Http\Controllers;

use Intranet\Botones\BotonImg;
use Intranet\Entities\Colaboracion;
use Illuminate\Support\Facades\Session;
use Mail;
use Intranet\Mail\DocumentRequest;
use Styde\Html\Facades\Alert;


/**
 * Class PanelColaboracionController
 * @package Intranet\Http\Controllers
 */
class PanelColaboracionController extends IntranetController
{

    /**
     * @var array
     */
    protected $gridFields = ['Empresa','concierto','Localidad','puestos','Xcolabora','contacto', 'telefono','email'];
    /**
     * @var string
     */
    protected $perfil = 'profesor';
    /**
     * @var string
     */
    protected $model = 'miscolaboraciones';


    /**
     *
     */
    protected function iniBotones()
    {
        $this->panel->setBotonera(['inicia','contacto','documentacion.request']);
        $this->panel->setBoton('grid', new BotonImg('miscolaboraciones.colabora.2', ['roles' => config('roles.rol.practicas'),'img'=>'fa-hand-o-down','where' => ['colabora', '!=', '2']]));
        $this->panel->setBoton('grid', new BotonImg('miscolaboraciones.colabora.1', ['roles' => config('roles.rol.practicas'),'img'=>'fa-hand-o-up','where' => ['colabora', '!=', '1']]));
        $this->panel->setBoton('grid', new BotonImg('miscolaboraciones.colabora.0', ['roles' => config('roles.rol.practicas'),'img'=>'fa-question','where' => ['colabora', '!=', '0']]));
        $this->panel->setBoton('grid', new BotonImg('miscolaboraciones.colabora.3', ['roles' => config('roles.rol.practicas'),'img'=>'fa-cloud','where' => ['colabora', '==', '0']]));
        $this->panel->setBoton('grid', new BotonImg('colaboracion.show',['roles' => [config('roles.rol.practicas'),config('roles.rol.dual')]]));
  
        Session::put('redirect', 'PanelColaboracionController@index');
    }

    /**
     * @return mixed
     */
    public function search(){
        return Colaboracion::MiColaboracion()->get();
    }

    /**
     * @param $id
     * @param $tipo
     * @return \Illuminate\Http\RedirectResponse
     */
    public function colabora($id, $tipo){
        $colaboracion = Colaboracion::find($id);
        $colaboracion->colabora = $tipo;
        $colaboracion->save();
        return $this->redirect();
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function inicia(){
        Colaboracion::MiColaboracion()->update(['colabora' => 0]);
        return $this->redirect();
    }

    /**
     * @param $document
     * @return \Illuminate\Http\RedirectResponse
     */
    public function emailDocuments($document){
        foreach (Colaboracion::MiColaboracion()->where('colabora',1)->get() as $colaboracion)
            $this->emailDocument($document,$colaboracion);
        return back();
    }

    /**
     * @param $document
     * @param $colaboracion
     */
    public function emailDocument($document, $colaboracion){
        // TODO : canviar AuthUser()->email per correu instructor
        Mail::to(AuthUser()->email, AuthUser()->ShortName)
            ->send(new DocumentRequest($colaboracion, AuthUser()->email
                ,config('fctEmails.'.$document.'.subject')
                ,config('fctEmails.'.$document.'.view')));
        Alert::info('Enviat correu a '.$colaboracion->Centro->nombre);
    }
}
