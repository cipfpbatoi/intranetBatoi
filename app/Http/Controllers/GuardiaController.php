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
        if ($ip) {
            $sesion = sesion(hora());
            $dia_semana = Hoy();
            $profesores = profesoresGuardia($dia_semana,$sesion);
            $estoyGuardia = estaGuardia(AuthUser()->dni,$dia_semana,$sesion);
            $horas = Hora::all();
            return view('guardias.guardia',compact('profesores','estoyGuardia','horas'));
        }
    }
}
