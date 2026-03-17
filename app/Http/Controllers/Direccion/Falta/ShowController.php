<?php

namespace Intranet\Http\Controllers\Direccion\Falta;

use Intranet\Entities\Falta;
use Intranet\Exceptions\NotFoundDomainException;
use Intranet\Http\Controllers\Controller;

/**
 * Detall d'una falta des del panell de Direcció.
 */
class ShowController extends Controller
{
    /**
     * Mostra el detall d'una falta.
     *
     * @param int|string $id
     * @throws NotFoundDomainException
     * @return \Illuminate\Contracts\View\View
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

        $elemento = $falta;
        $modelo = 'Falta';

        return view('intranet.show', compact('elemento', 'modelo'));
    }
}
