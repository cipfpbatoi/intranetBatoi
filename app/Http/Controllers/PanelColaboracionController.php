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
        $this->panel->setBoton('profile',new BotonIcon('colaboracion.switch', ['roles' => config('roles.rol.practicas'),'class'=>'btn-warning switch','icon'=>'fa-user','where' => ['tutor', '<>', AuthUser()->dni]]));

        $this->panel->setBoton('profile',new BotonIcon('colaboracion.unauthorize', ['roles' => config('roles.rol.practicas'),'class'=>'btn-primary unauthorize']));
        $this->panel->setBoton('profile',new BotonIcon('colaboracion.resolve', ['roles' => config('roles.rol.practicas'),'class'=>'btn-success resolve']));
        $this->panel->setBoton('profile',new BotonIcon('colaboracion.refuse', ['roles' => config('roles.rol.practicas'),'class'=>'btn-danger refuse']));
        $this->panel->setBothBoton('colaboracion.show',['text' => '','roles' => [config('roles.rol.practicas')]]);

        $this->panel->setBoton('profile',new BotonIcon('colaboracion.contacto', ['roles' => config('roles.rol.practicas'),'class'=>'btn-primary contacto','text'=>'','title'=>'Petició pràctiques','icon'=>'fa-envelope']));
        $this->panel->setBoton('profile',new BotonIcon('colaboracion.info', ['roles' => config('roles.rol.practicas'),'class'=>'btn-primary informe','text'=>'','title'=>'Revissió documentació','icon'=>'fa-envelope-o']));
        $this->panel->setBoton('profile',new BotonIcon('colaboracion.documentacion', ['roles' => config('roles.rol.practicas'),'class'=>'btn-primary informe','text'=>'','title'=>'Enviar documentació inici','icon'=>'fa-bell-o']));
        $this->panel->setBoton('profile',new BotonIcon('colaboracion.seguimiento', ['roles' => config('roles.rol.practicas'),'class'=>'btn-primary informe','text'=>'','title'=>'Correu seguiment','icon'=>'fa-phone']));
        $this->panel->setBoton('profile',new BotonIcon('colaboracion.visita', ['roles' => config('roles.rol.practicas'),'class'=>'btn-primary informe','text'=>'','title'=>'Concertar visita','icon'=>'fa-car']));
        $this->panel->setBoton('profile',new BotonIcon('colaboracion.student', ['roles' => config('roles.rol.practicas'),'class'=>'btn-primary informe','text'=>'','title'=>'Citar alumne','icon'=>'fa-bullhorn']));


        if (Colaboracion::where('estado', '=', 3)->count())
            $this->panel->setBoton('index', new BotonBasico("colaboracion.inicia",['icon' => 'fa fa-recycle']));
        if (Colaboracion::where('estado', '=', 1)->count())
            $this->panel->setBoton('index', new BotonBasico("colaboracion.contacto",['icon' => 'fa fa-envelope']));
        if (Colaboracion::where('estado', '=', 2)->count()){
            $this->panel->setBoton('index', new BotonBasico("colaboracion.info",['icon' => 'fa fa-envelope-o']));
            $this->panel->setBoton('index', new BotonBasico("colaboracion.documentacion",['icon' => 'fa fa-bell-o']));
            $this->panel->setBoton('index', new BotonBasico("colaboracion.seguimiento",['icon' => 'fa fa-phone']));
            $this->panel->setBoton('index', new BotonBasico("colaboracion.visita",['icon' => 'fa fa-car']));
            $this->panel->setBoton('index', new BotonBasico("colaboracion.student",['icon' => 'fa fa-bullhorn']));
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
        $colaboraciones = $this->selectColaboraciones($id,1);
        if ($colaboraciones->count() == 0) return back();
        return $this->sendEmails(config('fctEmails.contact'),$colaboraciones);
    }


    public function sendRequestInfo($id=null){
        $colaboraciones = $this->selectColaboraciones($id,2);
        if ($colaboraciones->count() == 0) return back();
        return $this->sendEmails(config('fctEmails.request'),$colaboraciones);
    }

    public function sendDocumentation($id=null){
        $colaboraciones = $this->selectColaboraciones($id,2);
        if ($colaboraciones->count() == 0) return back();
        return $this->sendEmails(config('fctEmails.info'),$colaboraciones);
    }

    public function sendStudent($id=null){
        $colaboraciones = $this->selectColaboraciones($id,2);
        if ($colaboraciones->count() == 0) return back();
        return $this->sendEmails(config('fctEmails.student'),$this->selectFctAlumnes($colaboraciones));
    }

    public function follow($id=null){
        $colaboraciones = $this->selectColaboraciones($id,2);
        if ($colaboraciones->count() == 0) return back();

        return $this->sendEmails(config('fctEmails.follow'),$this->selectFcts($colaboraciones));
    }

    public function visit($id=null){
        $colaboraciones = $this->selectColaboraciones($id,2);
        if ($colaboraciones->count() == 0) return back();

        return $this->sendEmails(config('fctEmails.visit'),$this->selectFcts($colaboraciones));
    }

    private function selectColaboraciones($id,$estado){
        return  $id?Colaboracion::where('id',$id)->get():Colaboracion::MiColaboracion()->where('tutor',AuthUser()->dni)->where('estado',$estado)->get();

    }

    private function selectFcts($colaboraciones){
        $fcts = collect();
        foreach ($colaboraciones as $colaboracion){
            foreach ($colaboracion->fcts as $fct)
                if ($fct->asociacion == 1 ) $fcts->push($fct);
        }
        return $fcts;
    }

    private function selectFctAlumnes($colaboraciones){
        $fctAl = collect();
        foreach ($this->selectFcts($colaboraciones) as $fct)
            foreach ($fct->Alumnos as $alumno){
                $fctAl->push($alumno);
            }
        return $fctAl;
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
