<?php

namespace Intranet\Http\Controllers;

use Intranet\Botones\BotonBasico;
use Intranet\Componentes\Mensaje;
use Intranet\Entities\Grupo;
use Intranet\Entities\AlumnoFctAval;
use Illuminate\Support\Facades\Mail;
use Intranet\Mail\TitolAlumne;
use Intranet\Services\AdviseService;
use Styde\Html\Facades\Alert;

/**
 * Class PanelActasController
 * @package Intranet\Http\Controllers
 */
class PanelActasController extends BaseController
{

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
    protected $gridFields = ['Nombre', 'hasta', 'horas', 'qualificacio', 'projecte'];
    /**
     * @var array
     */
    protected $vista = ['index' => 'intranet.list'] ;


    /**
     *
     */
    protected function iniBotones()
    {
        if (Grupo::findOrFail($this->search)->acta_pendiente) {
            $this->panel->setBoton('index', new BotonBasico("direccion.$this->search.finActa", ['text' => 'acta']));
            $this->panel->setBoton('index', new BotonBasico("direccion.$this->search.rejectActa", ['text' => 'reject']));
        }
    }

    /**
     * @return array|mixed
     */
    protected function search(){
        $grupo = Grupo::findOrFail($this->search);
        $this->titulo = ['quien' => $grupo->nombre ];
        if ($grupo->acta_pendiente) {
            return AlumnoFctAval::Grupo($grupo)->Pendiente()->get();
        }
        else {
            return [];
        }
    }

    /**
     * @param $idGrupo
     * @return \Illuminate\Http\RedirectResponse
     */
    public function finActa($idGrupo){
        $grupo = Grupo::findOrFail($idGrupo);
        $fcts = AlumnoFctAval::Grupo($grupo)->Pendiente()->get();
        $correus = 0;
        foreach ($fcts as $fct){
            $fct->actas = 2;
            $fct->save();
            /**if (isset($fct->Fct->Colaboracion->Centro->nombre)) {
                $empresas[$fct->Fct->Colaboracion->Centro->nombre] =
                    isset($empresas[$fct->Fct->Colaboracion->Centro->nombre])
                        ? $empresas[$fct->Fct->Colaboracion->Centro->nombre] . " , " . $fct->Alumno->FullName
                        : $fct->Alumno->FullName;
            }*/

            if ($fct->calificacion == 1){
                Mail::to($fct->Alumno->email)
                    ->send(new TitolAlumne($fct));
                $correus++;
            }

        }
        Alert::info("$correus enviats a Alumnes");
        $grupo->acta_pendiente = 0;
        $grupo->save();
        Mensaje::send($grupo->tutor, "Ja pots passar a arreplegar l'acta del grup $grupo->nombre", "#");
        return back();
    }

    public function rejectActa($idGrupo){
        $grupo = Grupo::findOrFail($idGrupo);
        $fcts = AlumnoFctAval::Grupo($grupo)->Pendiente()->get();
        $correus = 0;
        foreach ($fcts as $fct){
            $fct->actas = 0;
            $fct->save();
        }
        $grupo->acta_pendiente = 0;
        $grupo->save();
        Mensaje::send($grupo->tutor, "S'han detectat errades en l'acta de FCT del grup $grupo->nombre. Ja pots corregir-les");
        return back();
    }

    
}
