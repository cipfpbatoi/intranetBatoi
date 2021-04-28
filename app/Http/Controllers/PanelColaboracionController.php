<?php

namespace Intranet\Http\Controllers;

use Illuminate\View\View;
use Intranet\Botones\BotonIcon;
use Intranet\Botones\BotonBasico;
use Intranet\Entities\AlumnoFct;
use Intranet\Entities\Colaboracion;
use Illuminate\Support\Facades\Session;
use Intranet\Filters\ActivityAlreadyDone;
use Intranet\Filters\EmptyFinder;
use Intranet\Finders\ColaboracionFinder;
use Intranet\Botones\DocumentoFct;
use Intranet\Finders\UniqueFinder;
use Intranet\Services\DocumentFctService;
use Styde\Html\Facades\Alert;
use Illuminate\Http\Request;
use Intranet\Botones\Mail as myMail;
use Illuminate\Support\Collection;




/**
 * Class PanelColaboracionController
 * @package Intranet\Http\Controllers
 */
class PanelColaboracionController extends IntranetController
{
    use traitPanel;

    const ROLES_ROL_PRACTICAS = 'roles.rol.practicas';
    const FCT_EMAILS_REQUEST = 'fctEmails.request';
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

    protected $parametresVista = ['modal' => ['contacto','afegirFct','seleccion']];


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
        $this->panel->setBoton('profile',new BotonIcon('colaboracion.switch', ['roles' => config(self::ROLES_ROL_PRACTICAS),'class'=>'btn-warning switch','icon'=>'fa-user','where' => ['tutor', '<>', AuthUser()->dni]]));


        $this->panel->setBoton('nofct',new BotonIcon('colaboracion.unauthorize', ['roles' => config(self::ROLES_ROL_PRACTICAS),'class'=>'btn-primary unauthorize estado']));
        $this->panel->setBoton('nofct',new BotonIcon('colaboracion.resolve', ['roles' => config(self::ROLES_ROL_PRACTICAS),'class'=>'btn-success resolve estado']));
        $this->panel->setBoton('nofct',new BotonIcon('colaboracion.refuse', ['roles' => config(self::ROLES_ROL_PRACTICAS),'class'=>'btn-danger refuse estado']));

        $this->panel->setBoton('nofct',new BotonIcon('colaboracion.contacto', ['roles' => config(self::ROLES_ROL_PRACTICAS),'class'=>'btn-primary contacto','text'=>'','title'=>'Petició pràctiques','icon'=>'fa-bell-o']));
        $this->panel->setBoton('nofct',new BotonIcon('colaboracion.info', ['roles' => config(self::ROLES_ROL_PRACTICAS),'class'=>'btn-primary informe','text'=>'','title'=>'Revissió documentació','icon'=>'fa-check']));

        $this->panel->setBoton('fct',new BotonIcon('fct.documentacion', ['roles' => config(self::ROLES_ROL_PRACTICAS),'class'=>'btn-primary informe','text'=>'','title'=>'Enviar documentació inici','icon'=>'fa-flag-o']));
        $this->panel->setBoton('fct',new BotonIcon('fct.seguimiento', ['roles' => config(self::ROLES_ROL_PRACTICAS),'class'=>'btn-primary informe','text'=>'','title'=>'Correu seguiment','icon'=>'fa-envelope']));
        $this->panel->setBoton('fct',new BotonIcon('fct.telefonico', ['roles' => config(self::ROLES_ROL_PRACTICAS),'class'=>'btn-primary informe telefonico','text'=>'','title'=>'Contacte telefònic','icon'=>'fa-phone']));
        $this->panel->setBoton('fct',new BotonIcon('fct.visita', ['roles' => config(self::ROLES_ROL_PRACTICAS),'class'=>'btn-primary informe','text'=>'','title'=>'Concertar visita','icon'=>'fa-car']));

        $this->panel->setBoton('pendiente', new BotonBasico("colaboracion.contacto",['icon' => 'fa fa-bell-o']));

        $this->panel->setBoton('colabora', new BotonBasico("colaboracion.info",['class'=>'btn-primary','icon' => 'fa fa-check']));
        $this->panel->setBoton('colabora', new BotonBasico("colaboracion.documentacion",['class'=>'btn-info selecciona','icon' => 'fa fa-flag-o','data-url'=>'/api/colaboracion/'.AuthUser()->dni.'/info']));
        $this->panel->setBoton('colabora', new BotonBasico("fct.send",['class'=>'btn-info selecciona','icon' => 'fa fa-unlock','data-url'=>'/api/fct/'.AuthUser()->dni.'/documentation']));
        $this->panel->setBoton('colabora', new BotonBasico("colaboracion.seguimiento",['class'=>'btn-info selecciona','icon' => 'fa fa-envelope', 'fa fa-flag-o','data-url'=>'/api/fct/'.AuthUser()->dni.'/follow']));
        $this->panel->setBoton('colabora', new BotonBasico("colaboracion.visita",['icon' => 'fa fa-car']));
        $this->panel->setBoton('colabora', new BotonBasico("colaboracion.student",['icon' => 'fa fa-bullhorn']));


    }

    /**
     * @return mixed
     */
    public function search(){
        $colaboracions = Colaboracion::with('propietario')->with('Centro')->MiColaboracion()->orderBy('tutor')->get();
        if (count($colaboracions)){
            $this->titulo = ['quien' => $colaboracions->first()->Ciclo->literal];
        }
        return $colaboracions;
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */


    public function sendContact($id=null){
        $document = new DocumentoFct('contact');
        if ($id) {
            $finder = new UniqueFinder('',$id);
            $filter = new EmptyFilter($document);
        } else {
            $finder = new ColaboracionFinder(AuthUser()->dni,1);
            $filter = new ActivityAlreadyDone($document);
        }
        $service = new DocumentFctService($finder,$filter);
        return $document->exec();
    }

    public function info($dni){
        $document = new DocumentFctService('request',new ColaboracionFinder(AuthUser()->dni,1));
        $document->load();


        $finder = new ColaboracionFindService($dni,1,config('fctEmails.request'));
        return SelectColaboracionResource::collection($finder->exec());
    }

    public function sendRequest($id=null){
        $document = new DocumentoFct('request');
        if ($id) {
            $finder = new UniqueFinder('Colaboracion',$id);
            $filter = new EmptyFilter($document);
        } else {
            $finder = new ColaboracionFinder(AuthUser()->dni,2);
            $filter = new ActivityAlreadyDone($document);
        }
        $service = new DocumentFctService($finder,$filter);
        $service->exec();
        return back();
    }

    public function sendDocumentation($id=null){
        $fcts = $this->selectFcts($id,config('fctEmails.info'));
        if ($fcts->count() == 0) {
            return back();
        }
        if ($fcts->count() == 1) {
            return $this->sendEmails(config('fctEmails.infoU'),$fcts);
        }
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
        if ($fcts->count() == 1) {
            return $this->sendEmails(config('fctEmails.followU'),$fcts);
        }

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




    private function selectFctAlumnes(){
        $alumnos = collect();
        foreach (AlumnoFct::MisFcts()->get() as $fctAl) {
            $alumnos->push($fctAl->Alumno);
        }
        return $alumnos;
    }


    private function sendEmails($document,$colaboraciones){
        // Aci estic pintant-lo
        if ($document->redirect) {
            return $this->renderEmail($document, $colaboraciones);
        } else {
            $document->send($colaboraciones);
        }
        return back();
    }

    private function renderEmail($document,$colaboraciones){
        $elemento = $colaboraciones->first();
        $mail = new myMail($colaboraciones,$document['receiver'], $document['subject'], view($document['view'],compact('elemento')) );
        return $mail->render($document['redirect']);
    }

    protected function selecciona(Request $request){
        $colaboraciones = new Collection();
        foreach ($request->request as $item => $value){
            if ($value == 'on'){
                $colaboraciones->push(Colaboracion::find($item));
            }
        }
        if ($colaboraciones->count() == 1) {
            return $this->sendEmails(config('fctEmails.followU'),$fcts);
        }
        return $this->sendEmails(config(self::FCT_EMAILS_REQUEST),$colaboraciones);
    }

}
