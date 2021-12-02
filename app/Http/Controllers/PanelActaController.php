<?php

namespace Intranet\Http\Controllers;
use Intranet\Entities\Profesor;
use Intranet\Entities\Documento;
use Intranet\Entities\TipoDocumento;
use Illuminate\Support\Facades\Session;
use Styde\Html\Facades\Alert;


/**
 * Class PanelActaController
 * @package Intranet\Http\Controllers
 */
class PanelActaController extends BaseController
{

    /**
     * @var string
     */
    protected $perfil = 'profesor';
    /**
     * @var string
     */
    protected $model = 'Documento';

    /**
     * @param null $grupo
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function index($grupo=null)
    {
        Session::forget('redirect'); //buida variable de sessió redirect ja que sols se utiliza en cas de direccio
        $this->iniBotones();
        if ($this->iniPestanas($grupo)){
            return $this->grid($this->search($grupo),$this->modal);
        }
        Alert::danger('No hi ha actes disponibles');
        return redirect()->route('home');
     }


    /**
     * @param null $grupo
     * @return mixed
     */
    public function search($grupo = null)
    {
        $roles = RolesUser(AuthUser()->rol);
        $profe = Profesor::find(AuthUser()->dni);
        return Documento::whereIn('rol', $roles)
                ->whereIn('tipoDocumento', TipoDocumento::all($grupo))
                ->whereIn('grupo', $profe->grupos())
                ->orderBy('curso','desc')
                ->get();
    }

    /**
     * @param $grupos
     */
    private function createGrupsPestana($grupos){
        $first = false;
        foreach ($grupos as $grupo) {
            if ($first){
                $this->panel->setPestana($grupo->grupo, true, 'profile.documento', ['grupo', $grupo->grupo]);
            }
            else {
                $this->panel->setPestana($grupo->grupo, true, 'profile.documento', ['grupo', $grupo->grupo],null,1);
                $first = true;
            }
        }
    }

    /**
     * @param null $grupo
     * @return bool|void
     */
    protected function iniPestanas($grupo = null)
    {
        $roles = RolesUser(AuthUser()->rol);
        $profe = Profesor::find(AuthUser()->dni);
        $grupos = Documento::select('grupo')
                ->whereIn('rol', $roles)
                ->whereIn('tipoDocumento', TipoDocumento::all($grupo))
                ->whereIn('grupo', $profe->grupos())
                ->distinct()
                ->get();
        if ($grupos->count()){
            $this->createGrupsPestana($grupos);
            return true;
        }
        return false;
    }
    
    
}
