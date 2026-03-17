<?php

namespace Intranet\Http\Controllers\Direccion\Falta;

use Intranet\Entities\Falta;
use Intranet\Exceptions\NotFoundDomainException;
use Intranet\Http\Controllers\Controller;
use Intranet\Services\Document\DocumentPathService;

/**
 * Accés al document adjunt d'una falta des del panell de Direcció.
 */
class DocumentController extends Controller
{
    /**
     * Retorna el document físic associat a la falta.
     *
     * @param int|string $id
     * @throws NotFoundDomainException
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\RedirectResponse
     */
    public function __invoke($id)
    {
        $falta = Falta::find($id);
        if (!$falta) {
            throw new NotFoundDomainException('Falta no trobada', [
                'falta_id' => $id,
            ]);
        }

        $this->authorize('view', $falta);

        $path = $falta->fichero ? storage_path('app/' . $falta->fichero) : null;
        $pathService = new DocumentPathService();

        if ($path && $response = $pathService->responseFromPath($path)) {
            return $response;
        }

        throw new NotFoundDomainException(trans('messages.generic.nodocument'), [
            'model' => 'Falta',
            'id' => $id,
            'path' => $path,
        ]);
    }
}
