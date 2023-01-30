<?php

namespace Intranet\Http\Controllers;

use Intranet\Entities\Colaboracion;
use Illuminate\Support\Facades\Session;
use Styde\Html\Facades\Alert;


/**
 * Class PanelColaboracionController
 * @package Intranet\Http\Controllers
 */
class ColaboracionAlumnoController extends IntranetController
{
    use traitPanel;

    const ROLES_ROL_PRACTICAS = 'roles.rol.practicas';
    const FCT_EMAILS_REQUEST = 'fctEmails.request';
    /**
     * @var array
     */
    protected $gridFields = ['Empresa','Localidad','puestos','Xestado','contacto','email'];

    /**
     * @var string
     */
    protected $model = 'Colaboracion';

    //protected $parametresVista = ['modal' => ['contacto','afegirFct','seleccion']];


    /**
     * @return mixed
     */
    public function index()
    {
        $todos = $this->search();

        $this->crea_pestanas(
            config('modelos.'.$this->model.'.estados'),
            "profile.".strtolower($this->model),
            3,
            1,
            'situation'
        );
        Session::put('redirect', 'ColaboracionAlumnoController@index');
        return $this->grid($todos);
    }

    /**
     * @return mixed
     */
    public function search()
    {
        $tutor = AuthUser()->Grupo->first()?AuthUser()->Grupo->first()->tutor:null;
        if ($tutor) {
            $colaboracions = Colaboracion::with('propietario')->with('Centro')->MiColaboracion(null, $tutor)->get();
            if (count($colaboracions)) {
                $this->titulo = ['quien' => $colaboracions->first()->Ciclo->literal];
            }
            return $colaboracions->sortBy('tutor')->sortBy('localidad');
        } else {
            Alert::danger('No hem trobat el teu tutor');
            return collect();
        }

    }


}
