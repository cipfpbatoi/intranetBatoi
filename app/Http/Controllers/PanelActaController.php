<?php

namespace Intranet\Http\Controllers;

use Intranet\Application\Profesor\ProfesorService;
use Intranet\Http\Controllers\Core\BaseController;
use Intranet\Entities\Documento;
use Intranet\Services\Document\TipoDocumentoService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Session;
use Intranet\Services\UI\AppAlert as Alert;


/**
 * Class PanelActaController
 * @package Intranet\Http\Controllers
 */
class PanelActaController extends BaseController
{
    private ?ProfesorService $profesorService = null;

    /**
     * @var string
     */
    protected $perfil = 'profesor';
    /**
     * @var string
     */
    protected $model = 'Documento';

    private function profesores(): ProfesorService
    {
        if ($this->profesorService === null) {
            $this->profesorService = app(ProfesorService::class);
        }

        return $this->profesorService;
    }

    /**
     * @param null $grupo
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function index($grupo=null)
    {
        Gate::authorize('viewAny', Documento::class);
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
        Gate::authorize('viewAny', Documento::class);
        $roles = RolesUser(AuthUser()->rol);
        $profe = $this->profesores()->find((string) AuthUser()->dni);
        if (!$profe) {
            return collect();
        }
        return Documento::whereIn('rol', $roles)
                ->whereIn('tipoDocumento', TipoDocumentoService::all($grupo))
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
        $profe = $this->profesores()->find((string) AuthUser()->dni);
        if (!$profe) {
            return false;
        }
        $grupos = Documento::select('grupo')
                ->whereIn('rol', $roles)
                ->whereIn('tipoDocumento', TipoDocumentoService::all($grupo))
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
