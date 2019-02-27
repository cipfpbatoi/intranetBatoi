<?php

namespace Intranet\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Intranet\Botones\BotonImg;
use Intranet\Botones\BotonBasico;
use Intranet\Entities\Horario;
use Intranet\Entities\Grupo;
use Intranet\Entities\Modulo;
use Intranet\Botones\Panel;

/**
 * Class ModuloController
 * @package Intranet\Http\Controllers
 */
class ModuloController extends IntranetController
{

    /**
     * @var string
     */
    protected $perfil = 'profesor';
    /**
     * @var string
     */
    protected $model = 'Modulo';
    /**
     * @var array
     */
    protected $gridFields = ['codigo', 'vliteral','cliteral'];
    /**
     * @var bool
     */
    protected $modal = true;

    /**
     *
     */
    protected function iniBotones()
    {
        $this->panel->setBoton('grid', new BotonImg('modulo.edit', ['roles' => config('roles.rol.administrador')]));
    }

//    protected function asigna()
//    {
//        $todos = Modulo::all();
//        foreach ($todos as $uno) {
//            if ($uno->idCiclo == NULL){
//                $grupo = Horario::select('idGrupo')
//                        ->where('modulo', '=', $uno->codigo)
//                        ->first();
//                if ($grupo) {
//                    $uno->idCiclo = $grupo->Grupo->idCiclo;
//                    if (isset($grupo->Grupo->Ciclo->Departament->id))
//                        $uno->departamento = $grupo->Grupo->Ciclo->Departament->id;
//                    $uno->save();
//                }
//            }
//            
//            if ($uno->departamento == '99'){
//                switch ($uno->cliteral) {
//                    case 'Formación y orientación laboral':
//                    case 'Empresa e iniciativa emprendedora':
//                    case 'Relaciones en el equipo de trabajo':
//                    case 'FORMACIÓN Y ORIENTACIÓN LABORAL':
//                    case 'Formación y Orientación Laboral II':    
//                    case 'EMPRESA E INICIATIVA EMPRENDEDORA': $uno->departamento = 12;break;
//                    case 'Inglés Técnico I-M / Horario reservado para la docencia en inglés':
//                    case 'Inglés Técnico II-M / Horario reservado para la docencia en inglés':
//                    case 'Inglés Técnico I-S / Horario reservado para la docencia en inglés':
//                    case 'Inglés Técnico II-S / Horario reservado para la docencia en inglés':
//                    case 'Inglés'   : $uno->departamento = 1; break;
//                    default : 
//                        $grupo = Horario::select('idGrupo')
//                            ->where('modulo', '=', $uno->codigo)
//                            ->first();
//                        if ($grupo) {
//                            if (isset($grupo->Grupo->Ciclo->Departament->id))
//                                $uno->departamento = $grupo->Grupo->Ciclo->Departament->id;
//                        }
//                        break;
//                }
//                $uno->save();
//            }
//        }
//        return back();
//    }

}
