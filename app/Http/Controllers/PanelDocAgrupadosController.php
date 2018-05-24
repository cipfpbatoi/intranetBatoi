<?php

namespace Intranet\Http\Controllers;

use Intranet\Entities\Documento;
use Intranet\Entities\TipoDocumento;
use Illuminate\Support\Facades\Session;
use Styde\Html\Facades\Alert;


class PanelDocAgrupadosController extends BaseController
{
    
    protected $model = 'Documento';
    protected $profile = false;
    
    
    public function index($grupo=null)
    {
        $this->iniPestanas($grupo);
        return parent::index();
     }
    
    
    public function search()
    {
        return Documento::whereIn('rol', RolesUser(AuthUser()->rol))->whereIn('tipoDocumento',TipoDocumento::allDocuments())->whereNull('idDocumento')->get();
    }
    
    protected function iniPestanas($grupo= null)
    {
        $first = false;
        foreach (TipoDocumento::allRol($grupo) as $key => $role) {
            if (UserisAllow($role)){
                if ($first)  $this->panel->setPestana($key, true, 'profile.documento', ['tipoDocumento', $key]);
                else {
                    $this->panel->setPestana($key, true, 'profile.documento', ['tipoDocumento', $key],null,1);
                    $first = true;
                }
            }
        }
    }
    
    
}
