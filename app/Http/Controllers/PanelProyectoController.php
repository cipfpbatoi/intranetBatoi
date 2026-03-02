<?php

namespace Intranet\Http\Controllers;

use Intranet\Http\Controllers\Core\BaseController;

use Intranet\UI\Botones\BotonImg;
use Intranet\UI\Botones\BotonIcon;
use Intranet\Entities\Documento;
use Intranet\Entities\Ciclo;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Session;
use Intranet\Services\UI\AppAlert as Alert;


/**
 * Panell de projectes documentals per a professorat i alumnat.
 */
class PanelProyectoController extends BaseController
{
    
    protected $model = 'Documento';
    protected $gridFields = ['curso', 'descripcion', 'tags', 'ciclo'];

    /**
     * Mostra el panell de projectes documentals amb autorització prèvia.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $this->authorizeProyectoAccess();
        return parent::index();
    }
    
    
    protected function iniPestanas($parametres = null)
    {
        $this->authorizeProyectoAccess();
        $dep = isset(AuthUser()->departamento)?AuthUser()->departamento:AuthUser()->Grupo->first()->departamento;
        $ciclos = Ciclo::select('ciclo')
                 ->where('departamento', $dep)
                ->where('tipo',2)
                ->distinct()
                ->get();

        foreach ($ciclos as $ciclo) {
            $this->panel->setPestana(str_replace([' ', '(', ')', '.'], '', $ciclo->ciclo), true, 'profile.documento', ['ciclo', $ciclo->ciclo]);
        }
    }
    
    public function search()
    {
        $this->authorizeProyectoAccess();
        $dep = isset(AuthUser()->departamento)?AuthUser()->departamento:AuthUser()->Grupo->first()->departamento;
        $ciclos = hazArray(Ciclo::select('ciclo')
            ->where('departamento', $dep)
            ->where('tipo',2)
            ->distinct()
            ->get(),'ciclo','ciclo');

        return Documento::where('tipoDocumento', 'Proyecto')
                ->whereIn('ciclo',$ciclos)
                ->orderBy('curso','desc')
                ->get();
    }
    protected function iniBotones()
    {
        $this->panel->setBothBoton('documento.show');
    }

    /**
     * Autoritza l'accés al panell de projectes per a alumnat o professorat.
     *
     * @return void
     */
    private function authorizeProyectoAccess(): void
    {
        if ($this->isAlumnoUser()) {
            return;
        }

        Gate::authorize('viewAny', Documento::class);
    }

    /**
     * Comprova si l'usuari autenticat és alumne.
     *
     * @return bool
     */
    private function isAlumnoUser(): bool
    {
        $user = authUser();
        if (!is_object($user) || !isset($user->rol)) {
            return false;
        }

        return esRol((int) $user->rol, (int) config('roles.rol.alumno'));
    }
    
}
