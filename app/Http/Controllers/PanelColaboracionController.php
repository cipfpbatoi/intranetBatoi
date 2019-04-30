<?php

namespace Intranet\Http\Controllers;

use Illuminate\View\View;
use Intranet\Botones\BotonIcon;
use Intranet\Botones\BotonBasico;
use Intranet\Entities\Colaboracion;
use Illuminate\Support\Facades\Session;
use Intranet\Mail\DocumentRequest;
use Styde\Html\Facades\Alert;
use Intranet\Botones\Mail as myMail;




/**
 * Class PanelColaboracionController
 * @package Intranet\Http\Controllers
 */
class PanelColaboracionController extends IntranetController
{

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
     * @return mixed
     */
    public function index()
    {
        $todos = $this->search();

        $this->crea_pestanas(config('modelos.'.$this->model.'.estados'),"profile.".strtolower($this->model),1,1);
        $this->iniBotones();
        Session::put('redirect','PanelColaboracionController@index');
        return $this->grid($todos);
    }

    /**
     *
     */
    protected function iniBotones()
    {
        $this->panel->setBoton('profile',new BotonIcon('colaboracion.unauthorize', ['roles' => config('roles.rol.practicas'),'class'=>'btn-primary unauthorize']));
        $this->panel->setBoton('profile',new BotonIcon('colaboracion.resolve', ['roles' => config('roles.rol.practicas'),'class'=>'btn-success resolve']));
        $this->panel->setBoton('profile',new BotonIcon('colaboracion.refuse', ['roles' => config('roles.rol.practicas'),'class'=>'btn-danger refuse']));
        $this->panel->setBoton('profile',new BotonIcon('colaboracion.switch', ['roles' => config('roles.rol.practicas'),'class'=>'btn-warning switch','icon'=>'fa-user','where' => ['tutor', '<>', AuthUser()->dni]]));

        $this->panel->setBothBoton('colaboracion.show',['text' => '','roles' => [config('roles.rol.practicas')]]);
        $this->panel->setBoton('profile',new BotonIcon('colaboracion.contacto', ['roles' => config('roles.rol.practicas'),'text'=>'','icon'=>'fa-envelope','where' => ['estado', '==', '1']]));
        $this->panel->setBoton('profile',new BotonIcon('colaboracion.info', ['roles' => config('roles.rol.practicas'),'text'=>'','icon'=>'fa-envelope-o','where' => ['estado', '==', '2']]));
        $this->panel->setBoton('profile',new BotonIcon('colaboracion.documentacion', ['roles' => config('roles.rol.practicas'),'text'=>'','icon'=>'fa-bell-o','where' => ['estado', '==', '2']]));
        $this->panel->setBoton('profile',new BotonIcon('colaboracion.seguimiento', ['roles' => config('roles.rol.practicas'),'text'=>'','icon'=>'fa-phone','where' => ['estado', '==', '2']]));


        if (Colaboracion::where('estado', '=', 3)->count())
            $this->panel->setBoton('index', new BotonBasico("colaboracion.inicia",['icon' => 'fa fa-recycle']));
        if (Colaboracion::where('estado', '=', 1)->count())
            $this->panel->setBoton('index', new BotonBasico("colaboracion.contacto",['icon' => 'fa fa-envelope']));
        if (Colaboracion::where('estado', '=', 2)->count()){
            $this->panel->setBoton('index', new BotonBasico("colaboracion.info",['icon' => 'fa fa-envelope-o']));
            $this->panel->setBoton('index', new BotonBasico("colaboracion.documentacion",['icon' => 'fa fa-bell-o']));
            $this->panel->setBoton('index', new BotonBasico("colaboracion.seguimiento",['icon' => 'fa fa-phone']));
        }

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
        Colaboracion::MiColaboracion()->where('tutor',AuthUser()->dni)->update(['estado' => 1]);
        return $this->redirect();
    }

    public function sendFirstContact($id=null){
        if (!$colaboraciones = $this->selectColaboraciones($id,1)) return back();
        return $this->sendEmails(config('fctEmails.contact'),$colaboraciones);
    }

    public function sendRequestInfo($id=null){
        if (!$colaboraciones = $this->selectColaboraciones($id,2)) return back();
        return $this->sendEmails(config('fctEmails.request'),$colaboraciones);
    }

    public function sendDocumentation($id=null){
        if (!$colaboraciones = $this->selectColaboraciones($id,2)) return back();
        return $this->sendEmails(config('fctEmails.info'),$colaboraciones);
    }

    public function follow($id=null){
        if (!$colaboraciones = $this->selectColaboraciones($id,2)) return back();
        $fcts = collect();
        foreach ($colaboraciones as $colaboracion){
            foreach ($colaboracion->fcts as $fct)
            $fcts->push($fct);
        }
        return $this->sendEmails(config('fctEmails.follow'),$fcts);
    }

    public function visit($id=null){
        if (!$colaboraciones = $this->selectColaboraciones($id,2)) return back();
        $fcts = collect();
        foreach ($colaboraciones as $colaboracion){
            foreach ($colaboracion->fcts as $fct)
                $fcts->push($fct);
        }
        return $this->sendEmails(config('fctEmails.follow'),$fcts);
    }

    private function selectColaboraciones($id,$estado){
        return  $id?Colaboracion::where('id',$id)->get():Colaboracion::MiColaboracion()->where('tutor',AuthUser()->dni)->where('estado',$estado)->get();

    }

    private function sendEmails($document,$colaboraciones){
        if (isset($document['redirect'])) return $this->renderEmail($document,$colaboraciones);
        $mail = new myMail( $colaboraciones,$document['receiver'], $document['subject'], $document['view']);
        $mail->send();
        return back();
    }

    private function renderEmail($document,$colaboraciones){
        $elemento = $colaboraciones->first();
        $mail = new myMail( $colaboraciones,$document['receiver'], $document['subject'], view($document['view'],compact('elemento')) );
        return $mail->render($document['redirect']);

    }

}
