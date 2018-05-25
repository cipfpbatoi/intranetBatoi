<?php

namespace Intranet\Http\Controllers;
use Intranet\Entities\Profesor;
use Intranet\Entities\Documento;
use Intranet\Entities\TipoDocumento;
use Illuminate\Support\Facades\Session;
use Styde\Html\Facades\Alert;


class PanelActaController extends BaseController
{
    
    protected $perfil = 'profesor';
    protected $model = 'Documento';
   
    public function index($grupo=null)
    {
        Session::forget('redirect'); //buida variable de sessió redirect ja que sols se utiliza en cas de direccio
        $this->iniBotones();
        if ($this->iniPestanas($grupo))
            return $this->grid($this->search($grupo),$this->modal);
        else{
            Alert::danger('No hi ha actes disponibles');
            return redirect()->route('home');
        } 
            
     }
    
    
    public function search($grupo = null)
    {
        $roles = RolesUser(AuthUser()->rol);
        $profe = Profesor::find(AuthUser()->dni);
        return Documento::whereIn('rol', $roles)
                ->whereIn('tipoDocumento', TipoDocumento::all($grupo))
                ->whereIn('grupo', $profe->grupos())
                ->get();
    }
    
    protected function iniPestanas($grupo = null)
    {
        $first = false;
        $roles = RolesUser(AuthUser()->rol);
        $profe = Profesor::find(AuthUser()->dni);
        $grupos = Documento::select('grupo')
                ->whereIn('rol', $roles)
                ->whereIn('tipoDocumento', TipoDocumento::all($grupo))
                ->whereIn('grupo', $profe->grupos())
                ->distinct()
                ->get();
        if ($grupos){
            foreach ($grupos as $grupo) {
                if ($first) $this->panel->setPestana($grupo->grupo, true, 'profile.documento', ['grupo', $grupo->grupo]);
                    else {
                        $this->panel->setPestana($grupo->grupo, true, 'profile.documento', ['grupo', $grupo->grupo],null,1);
                        $first = true;
                    }

            }
            return true;
        }
        return false;
    }
    
    
}
