<?php

namespace Intranet\Http\Controllers;

use Intranet\Application\Grupo\GrupoService;
use Intranet\Http\Controllers\Core\BaseController;

use Intranet\UI\Botones\BotonBasico;
use Intranet\Services\Notifications\NotificationService;
use Intranet\Entities\AlumnoFctAval;
use Illuminate\Support\Facades\Mail;
use Intranet\Mail\TitolAlumne;
use Intranet\Services\Notifications\AdviseService;
use Styde\Html\Facades\Alert;

/**
 * Class PanelActasController
 * @package Intranet\Http\Controllers
 */
class PanelActasController extends BaseController
{
    private ?GrupoService $grupoService = null;

    /**
     * @var string
     */
    protected $perfil = 'profesor';
    /**
     * @var string
     */
    protected $model = 'AlumnoFctAval';
    /**
     * @var array
     */
    protected $gridFields = ['Nombre', 'hasta', 'horasTotal', 'qualificacio', 'projecte'];
    /**
     * @var array
     */
    protected $vista = ['index' => 'intranet.list'] ;

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


    /**
     *
     */
    protected function iniBotones()
    {
        $grupo = $this->grupos()->find((string) $this->search);
        abort_unless($grupo !== null, 404);
        if ($grupo->acta_pendiente) {
            $this->panel->setBoton(
                'index',
                new BotonBasico("direccion.$this->search.finActa", ['text' => 'acta'])
            );
            $this->panel->setBoton(
                'index',
                new BotonBasico("direccion.$this->search.rejectActa", ['text' => 'reject'])
            );
        }
    }

    /**
     * @return array|mixed
     */
    protected function search()
    {
        $grupo = $this->grupos()->find((string) $this->search);
        abort_unless($grupo !== null, 404);
        $this->titulo = ['quien' => $grupo->nombre ];
        if ($grupo->acta_pendiente) {
            return AlumnoFctAval::Grupo($grupo)->Pendiente()->get();
        }

        return [];
    }

    /**
     * @param $idGrupo
     * @return \Illuminate\Http\RedirectResponse
     */
    public function finActa($idGrupo)
    {
        $grupo = $this->grupos()->find((string) $idGrupo);
        abort_unless($grupo !== null, 404);
        $fcts = AlumnoFctAval::Grupo($grupo)->Pendiente()->get();
        $correus = 0;
        foreach ($fcts as $fct) {
            $fct->actas = 2;
            $fct->save();

            if ($fct->calificacion == 1) {
                Mail::to($fct->Alumno->email)
                    ->send(new TitolAlumne($fct));
                $correus++;
            }

        }
        Alert::info("$correus enviats a Alumnes");
        $grupo->acta_pendiente = 0;
        $grupo->save();
        app(NotificationService::class)->send($grupo->tutor, "Ja pots passar a arreplegar l'acta del grup $grupo->nombre", "#");
        return back();
    }

    public function rejectActa($idGrupo)
    {
        $grupo = $this->grupos()->find((string) $idGrupo);
        abort_unless($grupo !== null, 404);
        $fcts = AlumnoFctAval::Grupo($grupo)->Pendiente()->get();
        foreach ($fcts as $fct) {
            $fct->actas = 0;
            $fct->save();
        }
        $grupo->acta_pendiente = 0;
        $grupo->save();
        app(NotificationService::class)->send(
            $grupo->tutor,
            "S'han detectat errades en l'acta de FCT del grup $grupo->nombre. Ja pots corregir-les"
        );
        return back();
    }

    
}
