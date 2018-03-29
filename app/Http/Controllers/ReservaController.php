<?php

namespace Intranet\Http\Controllers;

use Intranet\Entities\Espacio;
use Intranet\Entities\Hora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ReservaController extends IntranetController
{
    protected $perfil = 'profesor';
    protected $model = 'Reserva';
    
    public function index()
    {
        Session::forget('redirect');
        $espacios = Espacio::where('reservable',1)->get();
        $horas = Hora::all();
        //dd($espacios);
        return view('reservas.reserva', compact('espacios', 'horas'));
    }
}
