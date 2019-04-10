<?php

namespace Intranet\Http\Controllers;

use Intranet\Botones\BotonIcon;
use Intranet\Botones\BotonImg;
use Intranet\Botones\BotonBasico;
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

    use traitPanel;
    /**
     * @var array
     */
    protected $gridFields = ['Empresa','concierto','Localidad','puestos','Xestado','contacto', 'telefono','email'];
    /**
     * @var string
     */
    protected $perfil = 'profesor';
    /**
     * @var string
     */
    protected $model = 'Colaboracion';



    /**
     *
     */
    protected function iniBotones()
    {
        $this->panel->setBothBoton('colaboracion.unauthorize', ['roles' => config('roles.rol.practicas'),'img'=>'fa-question','where' => ['estado', '>', '0','estado','==','3']]);
        $this->panel->setBothBoton('colaboracion.resolve', ['roles' => config('roles.rol.practicas'),'class'=>'btn-primary resolve','img'=>'fa-hand-o-up','where' => ['estado', '>', '0','estado','<>','3']]);
        $this->panel->setBothBoton('colaboracion.refuse', ['roles' => config('roles.rol.practicas'),'class'=>'btn-primary refuse','img'=>'fa-hand-o-down','where' => ['estado', '>', '0','estado','<>','4']]);
        $this->panel->setBothBoton('colaboracion.show',['img' => 'fa-eye','text' => '','roles' => [config('roles.rol.practicas')]]);
        if (Colaboracion::where('estado', '=', 4)->count())
            $this->panel->setBoton('index', new BotonBasico("colaboracion.inicia"));
        if (Colaboracion::where('estado', '=', 1)->count())
            $this->panel->setBoton('index', new BotonBasico("colaboracion.contacto"));
        if (Colaboracion::where('estado', '=', 3)->count())
        $this->panel->setBoton('index', new BotonBasico("colaboracion.documentacion"));
    }

    /**
     * @return mixed
     */
    public function search(){
        $this->titulo = ['quien' => Colaboracion::MiColaboracion()->first()->Ciclo->literal];
        return Colaboracion::MiColaboracion()->get();
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function inicia(){
        Colaboracion::MiColaboracion()->update(['estado' => 1]);
        return $this->redirect();
    }


    public function sendRequestInfo(){
        foreach (Colaboracion::MiColaboracion()->where('estado',2)->get() as $colaboracion)
            $this->emailDocument('request',$colaboracion);
        return back();
    }

    public function sendFirstContact(){
        foreach (Colaboracion::MiColaboracion()->where('estado',1)->get() as $colaboracion)
            $this->emailDocument('contact',$colaboracion);
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
        Alert::info('Enviat correu '.config('fctEmails.'.$document.'.subject').' a '.$colaboracion->Centro->nombre);
    }


}
