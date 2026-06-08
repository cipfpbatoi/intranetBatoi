<?php

namespace Intranet\Http\Controllers;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Intranet\Application\AlumnoFct\AlumnoFctService;
use Intranet\Application\Grupo\GrupoService;
use Intranet\Http\Controllers\Core\BaseController;

use Illuminate\Support\Facades\Gate;
use Intranet\UI\Botones\BotonImg;
use Intranet\Entities\Fct;

/**
 * Panell de control global de FCT per a direcció i cap de pràctiques.
 */
class PanelPracticasController extends BaseController
{
    private ?GrupoService $grupoService = null;
    private ?AlumnoFctService $alumnoFctService = null;

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
        'FctControl',
        'Xtutor'
    ];

    /**
     * Inicialitza les dependències del panell.
     */
    public function __construct(?GrupoService $grupoService = null, ?AlumnoFctService $alumnoFctService = null)
    {
        parent::__construct();
        $this->grupoService = $grupoService;
        $this->alumnoFctService = $alumnoFctService;
    }

    /**
     * Retorna el servei de grups.
     */
    private function grupos(): GrupoService
    {
        if ($this->grupoService === null) {
            $this->grupoService = app(GrupoService::class);
        }

        return $this->grupoService;
    }

    /**
     * Retorna el servei d'FCT de l'alumnat.
     */
    private function alumnoFcts(): AlumnoFctService
    {
        if ($this->alumnoFctService === null) {
            $this->alumnoFctService = app(AlumnoFctService::class);
        }

        return $this->alumnoFctService;
    }

    /**
     * Mostra el panell de control FCT amb autorització prèvia.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        Gate::authorize('manageFctControl', Fct::class);
        return parent::index();
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
        $grupos = $this->grupos()->withStudents();

        return $this->withFctControlStatus($grupos);


        /*$ciclos = hazArray(Ciclo::where('tipo',3)->get(),'id','id');
        return $this->grupos()->byCurso(2)
            ->orWhereIn('idCiclo',$ciclos)
            ->get();*/
    }

    /**
     * Afig estat agregat de validació documental i color de fila a cada grup.
     *
     * @param EloquentCollection<int, \Intranet\Entities\Grupo> $grupos
     * @return EloquentCollection<int, \Intranet\Entities\Grupo>
     */
    private function withFctControlStatus(EloquentCollection $grupos): EloquentCollection
    {
        foreach ($grupos as $grupo) {
            $status = $this->alumnoFcts()->controlStatsByGrupoEsFct((string) $grupo->codigo);
            $grupo->setAttribute('fct_control_status', $status);
            $grupo->class = trim(($grupo->class ?? '') . ' ' . $this->fctControlRowClass($status));
        }

        return $grupos;
    }

    /**
     * Resol la classe visual del grid segons l'estat documental.
     *
     * @param array{total: int, pg0301: int, a56: int, pg0301_complete: bool, a56_complete: bool} $status
     */
    private function fctControlRowClass(array $status): string
    {
        if (($status['a56_complete'] ?? false) === true) {
            return 'info';
        }

        if (($status['pg0301_complete'] ?? false) === true) {
            return 'success';
        }

        return '';
    }


    protected function show($id)
    {
        Gate::authorize('manageFctControl', Fct::class);
        $grupo = $this->grupos()->find((string) $id);
        abort_unless($grupo !== null, 404);
        return redirect()->route('fct.linkQuality',['dni'=>$grupo->tutor]);
    }

}
