<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Intranet\Entities\Guardia;
use Intranet\Entities\Hora;
use Intranet\Entities\Horario;
use Intranet\Entities\Profesor;
use Illuminate\Support\Facades\Session;

/**
 * Class GuardiaController
 * @package Intranet\Http\Controllers
 */
class GuardiaController extends IntranetController
{

    /**
     * @var string
     */
    protected $perfil = 'profesor';
    /**
     * @var string
     */
    protected $model = 'Guardia';

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function index()
    {
        Session::forget('redirect');
        $ip = getClientIpAddress();
        if ($ip )
        return view('guardias.guardia', ['horas'=> Hora::all()]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function control()
    {
        $horas = Hora::all();
        $dias  = array('L','M','X','J','V');
        $arrayG = array();
        foreach (Horario::Guardia()
                     ->orderBy('sesion_orden')
                     ->get() as $guardia){
            $profesor = Profesor::find($guardia->idProfesor);
            if ($profesor) {
                $profesorActual = $profesor->fecha_baja?($profesor->Sustituye??$profesor):$profesor;
                $arrayG[$guardia->sesion_orden][$guardia->dia_semana][] =  array('dni'=>$profesorActual->dni , 'nombre' =>$profesorActual->ShortName);
            }
        }

        return view('guardias.control',compact('horas','arrayG','dias'));
    }


    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function controlBiblio()
    {
        $horas = Hora::all();
        $dias  = array('L','M','X','J','V');
        foreach (Horario::GuardiaBiblio()
                     ->orderBy('sesion_orden')
                     ->get() as $guardia){
            $profesor = Profesor::findOrFail($guardia->idProfesor);
            if (isset($profesor->fecha_baja)) {
                $profesor = $profesor->Sustituye;
            }
            if ($profesor) {
                $arrayG[$guardia->sesion_orden][$guardia->dia_semana][] = array('dni' => $profesor->dni, 'nombre' => $profesor->ShortName);
            }
        }

        return view('guardias.control',compact('horas','arrayG','dias'));
    }

    /**
     * @param $fecha
     * @return array
     */
    public static function noGuardia($fecha)
    {
        $noGuardia = [];
        $guardias = Horario::Guardia()
                ->Dia(nameDay($fecha))
                ->get();
        foreach ($guardias as $guardia) {
            if (!Guardia::where('idProfesor', $guardia->idProfesor)
                ->where('dia', $fecha)
                ->where('hora', $guardia->sesion_orden)
                ->count()) {
                $noGuardia[$guardia->idProfesor] = $guardia->idProfesor;
            }
        }
        return $noGuardia;
    }        

}
