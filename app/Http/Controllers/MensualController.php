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
        switch ($request->llistat) {
            case 'faltas' : {
                $falta = new FaltaController();
                return $falta->imprime_falta($request);
            }
            case 'birret' : {
                $falta = new FaltaItacaController();
                return $falta->imprime_birret($request);
            }
        }
    }

    

}
