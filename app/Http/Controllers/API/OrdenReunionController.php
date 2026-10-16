<?php

namespace Intranet\Http\Controllers\API;

use Illuminate\Http\Request;
use Intranet\Entities\OrdenReunion;
use Intranet\Entities\Reunion;

/**
 * Controlador API dels punts d'una reunió.
 */
class OrdenReunionController extends ApiResourceController
{
    protected $model = 'OrdenReunion';

    /**
     * Llista només punts de reunions visibles per a l'usuari autenticat.
     */
    public function index()
    {
        $this->authorize('viewAny', Reunion::class);
        $orders = OrdenReunion::query()
            ->whereHas('Reunion', function ($query): void {
                $query->visibleTo(request()->user());
            })
            ->get();

        return $this->sendResponse($orders);
    }

    /**
     * Mostra un punt després d'autoritzar la reunió pare.
     *
     * @param int|string $id
     */
    public function show($id)
    {
        $order = $this->findOrder($id);
        $this->authorize('view', $order->Reunion);

        return $this->sendResponse($order);
    }

    /**
     * Retorna les dades d'edició d'un punt autoritzat.
     *
     * @param int|string $id
     */
    public function edit($id)
    {
        return $this->show($id);
    }

    /**
     * Crea un punt dins d'una reunió autoritzada.
     */
    public function store(Request $request)
    {
        $payload = $request->validate($this->storeRules());
        $reunion = $this->findModelOrFail(
            Reunion::class,
            $payload['idReunion'],
            'Reunió no trobada',
            ['reunion_id' => $payload['idReunion']]
        );
        $this->authorize('manageOrder', $reunion);
        $order = OrdenReunion::query()->create($payload);

        return $this->sendResponse(['created' => true, 'id' => $order->id], 'OK');
    }

    /**
     * Actualitza un punt sense permetre canviar-lo de reunió.
     *
     * @param int|string $id
     */
    public function update(Request $request, $id)
    {
        $order = $this->findOrder($id);
        $this->authorize('manageOrder', $order->Reunion);
        $order->update($request->validate($this->updateRules()));

        return $this->sendResponse(['updated' => true], 'OK');
    }

    /**
     * Elimina un punt autoritzat.
     *
     * @param int|string $id
     */
    public function destroy($id)
    {
        $order = $this->findOrder($id);
        $this->authorize('manageOrder', $order->Reunion);
        $order->delete();

        return $this->sendResponse(['deleted' => true], 'OK');
    }

    /**
     * Regles de validació per a creació.
     *
     * - `descripcion`: obliga a text breu.
     * - `resumen`: el límit màxim és de 65.535 caràcters (columna TEXT).
     */
    protected function storeRules(): array
    {
        return [
            'descripcion' => 'required|string|max:120',
            'resumen' => 'nullable|string|max:65535',
            'idReunion' => 'required|integer',
            'orden' => 'required|integer|between:1,127',
        ];
    }

    /**
     * Regles de validació per a actualització.
     *
     * Es permet partial update, però no canviar la reunió pare.
     */
    protected function updateRules(): array
    {
        return [
            'descripcion' => 'sometimes|string|max:120',
            'resumen' => 'sometimes|nullable|string|max:65535',
            'orden' => 'sometimes|integer|between:1,127',
        ];
    }

    /**
     * Resol un punt o genera una excepció de domini no trobat.
     *
     * @param int|string $id
     */
    private function findOrder($id): OrdenReunion
    {
        return $this->findModelOrFail(
            OrdenReunion::class,
            $id,
            'Punt de reunió no trobat',
            ['orden_reunion_id' => $id]
        );
    }
}
