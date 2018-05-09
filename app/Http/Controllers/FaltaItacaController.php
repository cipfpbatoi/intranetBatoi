<?php

namespace Intranet\Http\Controllers;

use Intranet\Entities\Hora;
use Intranet\Entities\Horario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Intranet\Botones\BotonIcon;

class FaltaItacaController extends BaseController
{
    use traitAutorizar;
    
    protected $perfil = 'profesor';
    protected $model = 'Falta_itaca';
    
    public function index()
    {
        Session::forget('redirect');
        $profesor = AuthUser();
        $horarios = Horario::Profesor($profesor->dni)->get();
        $horas = Hora::all();
        return view('falta.itaca', compact('profesor','horarios', 'horas'));
    }
}
