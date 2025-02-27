<?php
namespace Intranet\Services;

use Intranet\Finders\UniqueFinder;
use Intranet\Finders\RequestFinder;
use Intranet\Componentes\DocumentoFct;

class FctMailService
{
    public function getMailById($id, $documento)
    {
        $document = new DocumentoFct($documento);
        $parametres = ['id' => $id, 'document' => $document];
        $service = new DocumentService(new UniqueFinder($parametres));

        return $service->render();
    }

    public function getMailByRequest($request, $documento)
    {
        $document = new DocumentoFct($documento);
        $parametres = ['request' => $request, 'document' => $document];
        $service = new DocumentService(new RequestFinder($parametres));

        return $service->render();
    }
}
