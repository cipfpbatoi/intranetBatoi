<?php

namespace Intranet\Http\Controllers\Direccion\Comision;

use Intranet\Entities\Comision;
use Intranet\Exceptions\NotFoundDomainException;
use Intranet\Http\Controllers\Controller;

/**
 * Accés al document associat a una comissió des del panell Livewire de Direcció.
 */
class GestorController extends Controller
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

        throw new NotFoundDomainException(__('messages.generic.nodocument'), [
            'model' => 'Comision',
            'id' => $id,
        ]);
    }
}
