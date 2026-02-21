<?php

namespace Intranet\Http\Controllers;

use Intranet\Http\Controllers\Core\IntranetController;

use Intranet\Entities\Guardia;
use Intranet\Entities\Hora;
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
            $horas = Hora::all();
            $dia = hoy();
            $sesion = sesion(hora());
            $dni = authUser()->dni;

            $estoy = Guardia::query()
                ->Profesor($dni)
                ->DiaHora($dia, $sesion)
                ->exists();

            $guardiasAhora = $estoy
                ? Guardia::query()->DiaHora($dia, $sesion)->get()
                : collect();

            return view('guardias.guardia', compact('horas', 'estoy', 'guardiasAhora'));
        }
    }


}
