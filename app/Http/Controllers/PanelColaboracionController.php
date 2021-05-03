<?php

namespace Intranet\Http\Controllers;

use Intranet\Botones\BotonIcon;
use Intranet\Botones\BotonBasico;
use Intranet\Entities\Colaboracion;
use Intranet\Entities\Fct;
use Illuminate\Support\Facades\Session;
use Intranet\Finders\UniqueFinder;
use Intranet\Botones\DocumentoFct;
use Intranet\Finders\RequestFinder;
use Intranet\Services\DocumentService;
use Illuminate\Http\Request;





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

        $this->panel->setBoton('nofct',new BotonIcon('documentacionFCT.contacto', ['roles' => config(self::ROLES_ROL_PRACTICAS),'class'=>'btn-primary contacto','text'=>'','title'=>'Petició pràctiques','icon'=>'fa-bell-o']));
        $this->panel->setBoton('nofct',new BotonIcon('documentacionFCT.revision', ['roles' => config(self::ROLES_ROL_PRACTICAS),'class'=>'btn-primary informe','text'=>'','title'=>'Revissió documentació','icon'=>'fa-check']));

        $this->panel->setBoton('fct',new BotonIcon('documentacionFCT.inicioEmpresa', ['roles' => config(self::ROLES_ROL_PRACTICAS),'class'=>'btn-primary informe','text'=>'','title'=>'Enviar documentació inici','icon'=>'fa-flag-o']));
        $this->panel->setBoton('fct',new BotonIcon('documentacionFCT.seguimiento', ['roles' => config(self::ROLES_ROL_PRACTICAS),'class'=>'btn-primary informe','text'=>'','title'=>'Correu seguiment','icon'=>'fa-envelope']));
        $this->panel->setBoton('fct',new BotonIcon('fct.telefonico', ['roles' => config(self::ROLES_ROL_PRACTICAS),'class'=>'btn-primary informe telefonico','text'=>'','title'=>'Contacte telefònic','icon'=>'fa-phone']));
        $this->panel->setBoton('fct',new BotonIcon('documentacionFCT.visitaEmpresa', ['roles' => config(self::ROLES_ROL_PRACTICAS),'class'=>'btn-primary informe','text'=>'','title'=>'Concertar visita','icon'=>'fa-car']));

        $this->panel->setBoton('pendiente', new BotonBasico("colaboracion.contacto",['class'=>'btn-primary selecciona','icon' => 'fa fa-bell-o','data-url'=>'/api/documentacionFCT/contacto']));

        $this->panel->setBoton('colabora', new BotonBasico("colaboracion.revision",['class'=>'btn-primary selecciona','icon' => 'fa fa-check','data-url'=>'/api/documentacionFCT/revision']));
        $this->panel->setBoton('colabora', new BotonBasico("colaboracion.inicioEmpresa",['class'=>'btn-info selecciona','icon' => 'fa fa-flag-o','data-url'=>'/api/documentacionFCT/inicioEmpresa']));
        $this->panel->setBoton('colabora', new BotonBasico("colaboracion.inicioAlumno",['class'=>'btn-info selecciona','icon' => 'fa fa-unlock','data-url'=>'/api/documentacionFCT/inicioAlumno']));
        $this->panel->setBoton('colabora', new BotonBasico("colaboracion.seguimiento",['class'=>'btn-info selecciona','icon' => 'fa fa-envelope','data-url'=>'/api/documentacionFCT/seguimiento']));
        $this->panel->setBoton('colabora', new BotonBasico("colaboracion.visitaEmpresa",['class'=>'btn-info selecciona','icon' => 'fa fa-car','data-url'=>'/api/documentacionFCT/visitaEmpresa']));
        $this->panel->setBoton('colabora', new BotonBasico("colaboracion.citarAlumnos",['class'=>'btn-info selecciona','icon' => 'fa fa-bullhorn','data-url'=>'/api/documentacionFCT/citarAlumnos']));

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


    public function showMailbyId($id,$documento){
        $document = new DocumentoFct($documento);
        $parametres = array('id' => $id,'document'=>$document);
        $service = new DocumentService(new UniqueFinder($parametres));
        return $service->render();
    }


    protected function showMailbyRequest(Request $request,$documento){
        $documento = new DocumentoFct($documento);
        $parametres = array('request' => $request,'document'=>$documento);
        $service = new DocumentService(new RequestFinder($parametres));
        return $service->render();
    }


}
