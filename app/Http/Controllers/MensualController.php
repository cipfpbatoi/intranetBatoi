<?php

namespace Intranet\Http\Controllers;

use Intranet\Http\Requests\DesdeHastaRequest;


class MensualController extends Controller
{

    
    public function vistaImpresion()
    {
        return view('falta.imprime');
    }

    public function imprimir(DesdeHastaRequest $request)
    {
        if ($request->llistat == 'faltas') {
            return FaltaController::printReport($request);
        }

        return FaltaItacaController::printReport($request);
    }

    

}
