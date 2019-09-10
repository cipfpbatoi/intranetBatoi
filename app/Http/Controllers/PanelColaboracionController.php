<?php

namespace Intranet\Http\Controllers;

use Illuminate\View\View;
use Intranet\Botones\BotonIcon;
use Intranet\Botones\BotonBasico;
use Intranet\Entities\Colaboracion;
use Intranet\Entities\Fct;
use Illuminate\Support\Facades\Session;
use Intranet\Entities\Instructor;
use Intranet\Mail\DocumentRequest;
use Styde\Html\Facades\Alert;
use Illuminate\Http\Request;
use Intranet\Botones\Mail as myMail;
use Intranet\Entities\Activity;




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

    protected $parametresVista = ['modal' => ['contacto','afegirFct']];


    /**
     * @return mixed
     */
    public function index()
    {
        $todos = $this->search();

        $this->crea_pestanas(config('modelos.'.$this->model.'.estados'),"profile.".strtolower($this->model),2,1);
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


        $this->panel->setBoton('profile',new BotonIcon('colaboracion.unauthorize', ['roles' => config('roles.rol.practicas'),'class'=>'btn-primary unauthorize estado']));
        $this->panel->setBoton('profile',new BotonIcon('colaboracion.resolve', ['roles' => config('roles.rol.practicas'),'class'=>'btn-success resolve estado']));
        $this->panel->setBoton('profile',new BotonIcon('colaboracion.refuse', ['roles' => config('roles.rol.practicas'),'class'=>'btn-danger refuse estado']));

        $this->panel->setBoton('nofct',new BotonIcon('colaboracion.contacto', ['roles' => config('roles.rol.practicas'),'class'=>'btn-primary contacto','text'=>'','title'=>'Petició pràctiques','icon'=>'fa-bell-o']));
        $this->panel->setBoton('nofct',new BotonIcon('colaboracion.info', ['roles' => config('roles.rol.practicas'),'class'=>'btn-primary informe','text'=>'','title'=>'Revissió documentació','icon'=>'fa-check']));

        $this->panel->setBoton('fct',new BotonIcon('fct.documentacion', ['roles' => config('roles.rol.practicas'),'class'=>'btn-primary informe','text'=>'','title'=>'Enviar documentació inici','icon'=>'fa-flag-o']));
        $this->panel->setBoton('fct',new BotonIcon('fct.seguimiento', ['roles' => config('roles.rol.practicas'),'class'=>'btn-primary informe','text'=>'','title'=>'Correu seguiment','icon'=>'fa-envelope']));
        $this->panel->setBoton('fct',new BotonIcon('fct.telefonico', ['roles' => config('roles.rol.practicas'),'class'=>'btn-primary informe telefonico','text'=>'','title'=>'Contacte telefònic','icon'=>'fa-phone']));
        $this->panel->setBoton('fct',new BotonIcon('fct.visita', ['roles' => config('roles.rol.practicas'),'class'=>'btn-primary informe','text'=>'','title'=>'Concertar visita','icon'=>'fa-car']));
        //$this->panel->setBoton('fct',new BotonIcon('colaboracion.student', ['roles' => config('roles.rol.practicas'),'class'=>'btn-primary informe','text'=>'','title'=>'Citar alumne','icon'=>'fa-bullhorn']));

        $colaboraciones = Colaboracion::MiColaboracion()->where('tutor','=',AuthUser()->dni)->get();
            $this->panel->setBoton('pendiente', new BotonBasico("colaboracion.contacto",['icon' => 'fa fa-bell-o']));

            $this->panel->setBoton('colabora', new BotonBasico("colaboracion.info",['class'=>'btn-info','icon' => 'fa fa-check']));
            $this->panel->setBoton('colabora', new BotonBasico("colaboracion.documentacion",['class'=>'btn-info','icon' => 'fa fa-flag-o']));
            $this->panel->setBoton('colabora', new BotonBasico("colaboracion.seguimiento",['class'=>'btn-info','icon' => 'fa fa-envelope']));
            $this->panel->setBoton('colabora', new BotonBasico("colaboracion.visita",['icon' => 'fa fa-car']));
            $this->panel->setBoton('colabora', new BotonBasico("colaboracion.student",['icon' => 'fa fa-bullhorn']));


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


    public function sendFirstContact($id=null){
        $colaboraciones = $this->selectColaboraciones($id,1,config('fctEmails.contact'));
        if ($colaboraciones->count() == 0){
            Alert('No tens col.laboracions ','info');
            return back();
        }
        return $this->sendEmails(config('fctEmails.contact'),$colaboraciones);
    }


    public function sendRequestInfo($id=null){
        $colaboraciones = $this->selectColaboraciones($id,2,config('fctEmails.request'));
        if ($colaboraciones->count() == 0) return back();
        if ($colaboraciones->count() == 1) return $this->sendEmails(config('fctEmails.requestU'),$colaboraciones);
        return $this->sendEmails(config('fctEmails.request'),$colaboraciones);
    }

    public function sendDocumentation($id=null){
        $fcts = $this->selectFcts($id,config('fctEmails.info'));
        if ($fcts->count() == 0) return back();
        if ($fcts->count() == 1) return $this->sendEmails(config('fctEmails.infoU'),$fcts);
        return $this->sendEmails(config('fctEmails.info'),$fcts);
    }

    public function sendStudent($id=null){
        $alumnes = $this->selectFctAlumnes();
        if ($alumnes->count() == 0){
            Alert::info('No tens alumnes als que avisar');
            return back();
        }

        return $this->sendEmails(config('fctEmails.student'),$alumnes);
    }

    public function follow($id=null){
        $fcts = $this->selectFcts($id);

        if ($fcts->count() == 0){
            Alert::info('No tens empreses a les que fer el seguiment');
            return back();
        }
        if ($fcts->count() == 1) return $this->sendEmails(config('fctEmails.followU'),$fcts);

        return $this->sendEmails(config('fctEmails.follow'),$fcts);
    }

    public function visit($id=null){
        $fcts = $this->selectFcts($id);
        if ($fcts->count() == 0){
            Alert::info('No tens empreses a les que visitar');
            return back();
        }

        return $this->sendEmails(config('fctEmails.visit'),$fcts);
    }


    private function selectColaboraciones($id,$estado,$document=null){
        if ($id) return  Colaboracion::where('id',$id)->get();
        $colaboraciones = Colaboracion::MiColaboracion()->where('tutor',AuthUser()->dni)->where('estado',$estado)->get();
        if (!$document) return $colaboraciones;
        $noEnviadas = collect();
        foreach ($colaboraciones as $colaboracion){
            if (Activity::where('model_class','Intranet\Entities\Colaboracion')->where('model_id',$colaboracion->id)->where('document','=',$document['subject'])->count() == 0){
                if ($document['fcts'] && count($colaboracion->Fcts))
                    $noEnviadas->push($colaboracion);
                if (!$document['fcts'] && count($colaboracion->Fcts) == 0)
                    $noEnviadas->push($colaboracion);
            }

        }
        return $noEnviadas;
    }


    private function selectFcts($id,$document=null){
        if ($id) return  Fct::where('id',$id)->get();
        $fcts = Fct::MisFcts()->EsFct()->get();
        if (!$document) return $fcts;
        $noRepeat = collect();
        foreach ($fcts as $fct){
            if ($fct->Nalumnes > 0 && $fct->correoInstructor == 0 && Activity::where('model_class','Intranet\Entities\Fct')->where('model_id',$fct->id)->where('document','=',$document['subject'])->count() == 0)
                $noRepeat->push($fct);
        }
        return $noRepeat;
    }


    private function selectFctAlumnes(){
        $fctAl = collect();
        foreach ($this->selectFcts(null) as $fct)
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
