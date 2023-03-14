<?php

namespace Intranet\Http\Controllers;

use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Intranet\Botones\BotonIcon;
use Intranet\Botones\BotonBasico;
use Intranet\Entities\Centro;
use Intranet\Entities\Colaboracion;
use Illuminate\Support\Facades\Session;
use Intranet\Entities\Grupo;
use Intranet\Finders\UniqueFinder;
use Intranet\Componentes\DocumentoFct;
use Intranet\Finders\RequestFinder;
use Intranet\Services\DocumentService;
use Illuminate\Http\Request;
use Styde\Html\Facades\Alert;
use Illuminate\Support\Facades\DB;

/**
 * Class PanelColaboracionController
 * @package Intranet\Http\Controllers
 */
class FctMailController extends Controller
{

    /**
     * @return \Illuminate\Http\RedirectResponse
     */


    public function showMailbyId($id, $documento)
    {
        $document = new DocumentoFct($documento);
        $parametres = array('id' => $id, 'document' => $document);
        $service = new DocumentService(new UniqueFinder($parametres));

        return $service->render();
    }


    protected function showMailbyRequest(Request $request, $documento)
    {
        $documento = new DocumentoFct($documento);
        $parametres = array('request' => $request, 'document' => $documento);
        $service = new DocumentService(new RequestFinder($parametres));
        return $service->render();
    }

}
