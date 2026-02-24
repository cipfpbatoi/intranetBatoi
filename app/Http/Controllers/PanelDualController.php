<?php

namespace Intranet\Http\Controllers;

use Intranet\Application\Grupo\GrupoService;
use Intranet\Http\Controllers\Core\BaseController;

use Intranet\UI\Botones\BotonImg;
use Intranet\Entities\Fct;
use Illuminate\Support\Facades\Gate;

class PanelDualController extends BaseController
{
    private ?GrupoService $grupoService = null;

    protected $perfil = 'profesor';
    protected $model = 'Grupo';
    protected $gridFields = [
        'nombre',
        'Matriculados',
        'EnDual',
        'XDual'
    ];

    public function __construct(?GrupoService $grupoService = null)
    {
        parent::__construct();
        $this->grupoService = $grupoService;
    }

    private function grupos(): GrupoService
    {
        if ($this->grupoService === null) {
            $this->grupoService = app(GrupoService::class);
        }

        return $this->grupoService;
    }
    
    protected function iniBotones()
    {
        Gate::authorize('manageDualControl', Fct::class);
        $this->panel->setBoton('grid',new BotonImg(
                'fctcap.dual',
                [
                    'img' => 'fa-bullseye',
                    'roles' => config('roles.rol.jefe_practicas')
                ])
        );
        $this->panel->setBoton('grid',new BotonImg(
            'fctcap.show',
            [
                'img' => 'fa-eye',
                'roles' => config('roles.rol.jefe_practicas')
            ])
        );

        
    }
    protected function search()
    {
        Gate::authorize('manageDualControl', Fct::class);
        $duals = Fct::esDual()->get();
        $grups = [];
        foreach ($duals as $dual){
            foreach ($dual->Alumnos as $alumne){
                $grupo = $alumne->Grupo->first()->codigo;
                $grups[$grupo] = $grupo;
            }
        }
        return $this->grupos()->byCodes(array_values($grups));
    }

    protected function show($id)
    {
        Gate::authorize('manageDualControl', Fct::class);
        $grupo = $this->grupos()->find((string) $id);
        abort_unless($grupo !== null, 404);
        return redirect()->route('fct.linkQuality',['dni'=>$grupo->tutor]);
    }

}
