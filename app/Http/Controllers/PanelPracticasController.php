<?php

namespace Intranet\Http\Controllers;

use Intranet\Application\Grupo\GrupoService;
use Intranet\Http\Controllers\Core\BaseController;

use Illuminate\Support\Facades\Gate;
use Intranet\UI\Botones\BotonImg;
use Intranet\Entities\Fct;

class PanelPracticasController extends BaseController
{
    private ?GrupoService $grupoService = null;

    protected $perfil = 'profesor';
    protected $model = 'Grupo';
    protected $gridFields = [
        'nombre',
        'Matriculados',
        'Resfct',
        'Exentos',
        'Respro',
        'Resempresa',
        'Acta',
        'Calidad',
        'Xtutor'
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
        Gate::authorize('manageFctControl', Fct::class);
        $this->panel->setBoton('grid',new BotonImg(
            'direccion.acta',
            [
                'img' => 'fa-file-word-o',
                'roles' => config('roles.rol.direccion'),
                'where' => ['acta_pendiente','==','1']
            ])
        );
        $this->panel->setBoton('grid',new BotonImg(
            'fctcap.check',
            [
                'img' => 'fa-check',
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
        Gate::authorize('manageFctControl', Fct::class);
        return $this->grupos()->withStudents();


        /*$ciclos = hazArray(Ciclo::where('tipo',3)->get(),'id','id');
        return $this->grupos()->byCurso(2)
            ->orWhereIn('idCiclo',$ciclos)
            ->get();*/
    }


    protected function show($id)
    {
        Gate::authorize('manageFctControl', Fct::class);
        $grupo = $this->grupos()->find((string) $id);
        abort_unless($grupo !== null, 404);
        return redirect()->route('fct.linkQuality',['dni'=>$grupo->tutor]);
    }

}
