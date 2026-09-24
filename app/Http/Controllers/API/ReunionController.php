<?php

namespace Intranet\Http\Controllers\API;

use Illuminate\Http\Request;
use Intranet\Entities\Reunion;

/**
 * Controlador API per a reunions amb autorització explícita.
 */
class ReunionController extends ApiResourceController
{
    protected $model = 'Reunion';

    /**
     * Llista només les reunions visibles per al professor autenticat.
     */
    public function index()
    {
        $this->authorize('viewAny', Reunion::class);

        return $this->sendResponse(Reunion::query()->visibleTo(request()->user())->get());
    }

    /**
     * Mostra una reunió autoritzada.
     *
     * @param int|string $id
     */
    public function show($id)
    {
        $reunion = $this->findReunion($id);
        $this->authorize('view', $reunion);

        return $this->sendResponse($reunion);
    }

    /**
     * Retorna les dades d'edició d'una reunió autoritzada.
     *
     * @param int|string $id
     */
    public function edit($id)
    {
        return $this->show($id);
    }

    /**
     * Crea una reunió i força el professor autenticat com a convocant.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Reunion::class);
        $payload = $request->validate($this->storeRules());
        $payload['idProfesor'] = (string) $request->user()->dni;
        $reunion = Reunion::query()->create($payload);

        return $this->sendResponse(['created' => true, 'id' => $reunion->id], 'OK');
    }

    /**
     * Actualitza exclusivament els camps funcionals permesos.
     *
     * @param int|string $id
     */
    public function update(Request $request, $id)
    {
        $reunion = $this->findReunion($id);
        $this->authorize('update', $reunion);
        $reunion->update($request->validate($this->updateRules()));

        return $this->sendResponse(['updated' => true], 'OK');
    }

    /**
     * Elimina una reunió oberta autoritzada.
     *
     * @param int|string $id
     */
    public function destroy($id)
    {
        $reunion = $this->findReunion($id);
        $this->authorize('delete', $reunion);
        $reunion->delete();

        return $this->sendResponse(['deleted' => true], 'OK');
    }

    /**
     * Actualitza la valoració d'un alumne que ja pertany a la reunió.
     *
     * @param int|string $idReunion
     * @param int|string $idAlumno
     */
    public function putAlumno($idReunion, $idAlumno, Request $request)
    {
        $reunion = $this->findReunion($idReunion);
        $this->authorize('manageParticipants', $reunion);
        $validated = $request->validate([
            'capacitats' => 'required|integer|between:0,127',
        ]);

        if (!$reunion->alumnos()->where('alumnos.nia', (string) $idAlumno)->exists()) {
            return $this->sendNotFound("L'alumne no pertany a la reunió indicada.");
        }

        $reunion->alumnos()->updateExistingPivot($idAlumno, [
            'capacitats' => (int) $validated['capacitats'],
        ]);

        return $this->sendResponse((int) $validated['capacitats'], 'OK');
    }

    /**
     * Regles per a crear reunions des de l'API.
     *
     * @return array<string, string>
     */
    protected function storeRules(): array
    {
        return [
            'tipo' => 'required|integer',
            'grupo' => 'nullable',
            'idGrupo' => 'nullable|string|max:10',
            'curso' => 'required|string|max:20',
            'numero' => 'nullable|integer',
            'fecha' => 'required|date',
            'descripcion' => 'required|string|max:120',
            'objetivos' => 'nullable|string',
            'idEspacio' => 'required|string|max:10',
        ];
    }

    /**
     * Regles per a actualitzar reunions sense permetre propietat ni arxiu.
     *
     * @return array<string, string>
     */
    protected function updateRules(): array
    {
        return [
            'tipo' => 'sometimes|integer',
            'grupo' => 'sometimes|nullable',
            'idGrupo' => 'sometimes|nullable|string|max:10',
            'curso' => 'sometimes|string|max:20',
            'numero' => 'sometimes|nullable|integer',
            'fecha' => 'sometimes|date',
            'descripcion' => 'sometimes|required|string|max:120',
            'objetivos' => 'sometimes|nullable|string',
            'idEspacio' => 'sometimes|required|string|max:10',
        ];
    }

    /**
     * Resol una reunió o genera una excepció de domini no trobat.
     *
     * @param int|string $id
     */
    private function findReunion($id): Reunion
    {
        return $this->findModelOrFail(
            Reunion::class,
            $id,
            'Reunió no trobada',
            ['reunion_id' => $id]
        );
    }
}
