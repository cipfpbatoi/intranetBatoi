<?php

namespace Intranet\Http\Controllers;

use Intranet\Entities\Documento;
use Intranet\Entities\TipoDocumento;
use Illuminate\Support\Facades\Session;
use Styde\Html\Facades\Alert;


class PanelDocAgrupadosController extends BaseController
{
    
    protected $perfil = 'profesor';
    protected $model = 'Documento';
    protected $gridFields = ['curso', 'descripcion', 'tags', 'ciclo','detalle'];
    
    
    public function grupo($grupo)
    {
        Session::forget('redirect'); 
        $this->iniBotones();
        $this->iniPestanas($grupo);
        return $this->grid($this->search());
     }
    
    
    public function search()
    {
        return Documento::whereIn('rol', RolesUser(AuthUser()->rol))->get();
    }
    
    protected function iniPestanas($parametres = null)
    {
        $first = false;
        foreach (TipoDocumento::allRol($parametres) as $key => $role) {
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
