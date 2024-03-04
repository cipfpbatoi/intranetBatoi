<?php

namespace Intranet\Http\Controllers;


use Intranet\Finders\UniqueFinder;
use Intranet\Componentes\DocumentoFct;
use Intranet\Finders\RequestFinder;
use Intranet\Services\DocumentService;
use Illuminate\Http\Request;

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
