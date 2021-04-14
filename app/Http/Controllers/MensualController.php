<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;


class MensualController extends Controller
{

    
    public function vistaImpresion()
    {
        return view('falta.imprime');
    }

    public function imprimir(Request $request)
    {
        if ($request->llistat == 'faltas') {
            return FaltaController::printReport($request);
        }

        return FaltaItacaController::printReport($request);
    }

    

}
