<?php

namespace Intranet\Http\Controllers;

use Intranet\Entities\Documento;
use Intranet\Entities\TipoDocumento;
use Illuminate\Support\Facades\Session;
use Styde\Html\Facades\Alert;


/**
 * Class PanelDocAgrupadosController
 * @package Intranet\Http\Controllers
 */
class PanelDocAgrupadosController extends BaseController
{

    /**
     * @var string
     */
    protected $model = 'Documento';
    /**
     * @var bool
     */
    protected $profile = false;


    /**
     * @param null $grupo
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function index($grupo=null)
    {
        $this->iniPestanas($grupo);
        return parent::index();
     }


    /**
     * @return mixed
     */
    public function search()
    {
        return Documento::whereIn('rol', RolesUser(AuthUser()->rol))->whereIn('tipoDocumento',TipoDocumento::allDocuments())->whereNull('idDocumento')
            ->orderBy('curso','desc')->get();
    }

    /**
     * @param null $grupo
     */
    protected function iniPestanas($grupo= null)
    {
        $first = false;
        foreach (TipoDocumento::allRol($grupo) as $key => $role) {
            if (UserisAllow($role)){
                if ($first)  {
                    $this->panel->setPestana($key, true, 'profile.documento', ['tipoDocumento', $key]);
                }
                else {
                    $this->panel->setPestana($key, true, 'profile.documento', ['tipoDocumento', $key],null,1);
                    $first = true;
                }
            }
        }
    }
    
    
}
