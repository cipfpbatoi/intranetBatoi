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

        $this->panel->setBothBoton('colaboracion.show',['img' => 'fa-eye','text' => '','roles' => [config('roles.rol.practicas')]]);
        $this->panel->setBoton('profile',new BotonIcon('colaboracion.contacto', ['roles' => config('roles.rol.practicas'),'img'=>'fa-envelope','where' => ['estado', '==', '1']]));
        $this->panel->setBoton('profile',new BotonIcon('colaboracion.documentacion', ['roles' => config('roles.rol.practicas'),'img'=>'fa-envelope','where' => ['estado', '==', '2']]));


        if (Colaboracion::where('estado', '=', 3)->count())
            $this->panel->setBoton('index', new BotonBasico("colaboracion.inicia",['icon' => 'fa fa-recycle']));
        if (Colaboracion::where('estado', '=', 1)->count())
            $this->panel->setBoton('index', new BotonBasico("colaboracion.contacto",['icon' => 'fa fa-envelope']));
        if (Colaboracion::where('estado', '=', 2)->count())
            $this->panel->setBoton('index', new BotonBasico("colaboracion.info",['icon' => 'fa fa-envelope-o']));
        if (Colaboracion::where('estado', '=', 2)->count())
            $this->panel->setBoton('index', new BotonBasico("colaboracion.documentacion",['icon' => 'fa fa-envelope-o']));

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


    public function sendRequestInfo($id=null){
        $colaboraciones = $id?Colaboracion::where('id',$id)->get():Colaboracion::MiColaboracion()->where('tutor',AuthUser()->dni)->where('estado',2)->get();
        if (!$colaboraciones) return back();
        return $this->emailDocument(config('fctEmails.request'),$colaboraciones);
    }

    public function sendFirstContact($id=null){
        $colaboraciones = $id?Colaboracion::where('id',$id)->get():Colaboracion::MiColaboracion()->where('tutor',AuthUser()->dni)->where('estado',1)->get();
        if (!$colaboraciones) return back();
        return $this->emailDocument(config('fctEmails.contact'),$colaboraciones);
    }



    public function sendDocumentation($id=null){
        $colaboraciones = $id?Colaboracion::where('id',$id)->get():Colaboracion::MiColaboracion()->where('tutor',AuthUser()->dni)->where('estado',2)->get();
        if (!$colaboraciones) return back();
        return $this->emailDocument(config('fctEmails.request'),$colaboraciones);
    }

    private function emailDocument($document,$colaboraciones){

        $elemento = $colaboraciones->first();

        if (isset($document['redirect'])){
            $content = view($document['view'],compact('elemento'));
            $mail = new myMail( $this->getReceiver($colaboraciones),$document['receiver'], $document['subject'], $content );
            return $mail->render($document['redirect']);
        }
        $mail = new myMail( $colaboraciones,$document['receiver'], $document['subject'], $document['view']);
        $mail->renderAndSend();
        return back();

    }

    private function getReceiver($colaboraciones){
        $to = '';
        foreach ($colaboraciones as $colaboracion){
            $to .= $colaboracion->email.'('.$colaboracion->contacto.'),';
        }
        return $to;
    }


    /**
     * @param $document
     * @param $colaboracion

    public function emailDocument($document, $colaboracion){
        // TODO : canviar AuthUser()->email per correu instructor
        Mail::to(AuthUser()->email, AuthUser()->ShortName)
            ->send(new DocumentRequest($colaboracion, AuthUser()->email
                ,config('fctEmails.'.$document.'.subject')
                ,config('fctEmails.'.$document.'.view')));
        Alert::info('Enviat correu '.config('fctEmails.'.$document.'.subject').' a '.$colaboracion->Centro->nombre);
    }
     */

}
