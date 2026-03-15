<?php

namespace Intranet\Http\Controllers;

use Intranet\Entities\Comision;
use Intranet\Exceptions\NotFoundDomainException;

/**
 * Accés al document associat a una comissió des del panell Livewire de Direcció.
 */
class ComisionDireccionGestorController extends Controller
{
    /**
     * Redirigeix al gestor documental si la comissió té document associat.
     *
     * @param int|string $id
     * @throws NotFoundDomainException
     * @return \Illuminate\Http\RedirectResponse
     */
    public function __invoke($id)
    {
        $comision = Comision::findOrFail($id);
        $this->authorize('view', $comision);

        if ($comision->idDocumento) {
            return redirect('/documento/' . $comision->idDocumento . '/show');
        }

        throw new NotFoundDomainException(trans('messages.generic.nodocument'), [
            'model' => 'Comision',
            'id' => $id,
        ]);
    }
}
