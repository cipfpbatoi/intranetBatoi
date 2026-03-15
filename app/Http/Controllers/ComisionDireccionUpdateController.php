<?php

namespace Intranet\Http\Controllers;

use Intranet\Entities\Comision;
use Intranet\Http\Requests\ComisionRequest;

/**
 * Actualització específica de comissions des del panell Livewire de Direcció.
 *
 * Manté el formulari legacy com a bridge temporal, però evita dependre del
 * controller generalista de comissions per a l'edició del pilot nou.
 */
class ComisionDireccionUpdateController extends Controller
{
    /**
     * Actualitza la comissió indicada i torna al panell Livewire.
     *
     * @param ComisionRequest $request
     * @param int|string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function __invoke(ComisionRequest $request, $id)
    {
        $comision = Comision::findOrFail($id);
        $this->authorize('update', $comision);
        $comision->fillAll($request);

        return redirect()->route('comision.direccion.livewire')
            ->with('success', 'Comissió actualitzada correctament.');
    }
}
