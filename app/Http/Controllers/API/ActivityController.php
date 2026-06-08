<?php

namespace Intranet\Http\Controllers\API;


use Illuminate\Http\Request;
use Intranet\Application\Seguimiento\SeguimientoService;
use Intranet\Entities\Activity;
use Intranet\Exceptions\NotFoundDomainException;

/**
 * Controlador API d'activitats.
 */
class ActivityController extends ApiResourceController
{
    protected $model = 'Activity';

    public function __construct(private readonly SeguimientoService $seguimientoService)
    {
        parent::__construct();
    }

    /**
     * @param int|string $id
     * @param int|string $fct
     * @throws NotFoundDomainException
     * @return \Illuminate\Http\JsonResponse
     */
    public function move($id, $fct)
    {
        $activity = $this->findModelOrFail(Activity::class, $id, 'Activitat no trobada', ['activity_id' => $id]);
        if ($activity->model_id == $fct) {
            return $this->sendFail("Tria una altra FCT");
        } else {
            $activity2 = Activity::where('action', $activity->action)
                ->where('model_id', $fct)
                ->where('created_at', '>', Hoy('Y-m-d'))
                ->where('created_at', '<', manana('Y-m-d'))
                ->first();
            if ($activity2) {
                return $this->sendFail('Eixa evidencia ja existeix');
            }
            $activity2 = new Activity();
            $activity2->model_id = $fct;
            $activity2->action = $activity->action;
            $activity2->model_class = $activity->model_class;
            $activity2->author_id = $activity->author_id;
            $activity2->created_at = $activity->created_at;
            $activity2->updated_at = $activity->updated_at;
            $activity2->document = $activity->document;
            $activity2->comentari = null;
            $activity2->save();

            $this->syncSeguimientoMirror($activity2);

            return $this->sendResponse(['id'=>$activity2->id], 'OK');
        }

    }

    /**
     * Mostra una activitat amb el format JSON esperat pels modals de contacte.
     *
     * @param int|string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $activity = Activity::find($id);
        if ($activity === null) {
            return $this->sendNotFound('Activitat no trobada');
        }

        return $this->sendResponse([
            'id' => (string) $activity->id,
            'comentari' => $activity->comentari,
            'document' => $activity->document,
            'action' => $activity->action,
        ], 'OK');
    }

    /**
     * Actualitza el comentari d'una activitat i sincronitza el mirall de seguiment.
     *
     * @param Request $request
     * @param int|string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $activity = Activity::find($id);
        if ($activity === null) {
            return $this->sendNotFound('Activitat no trobada');
        }

        $activity->comentari = (string) $request->input('explicacion', $request->input('comentari', ''));
        $activity->save();
        $this->syncSeguimientoMirror($activity);

        return $this->sendResponse([
            'id' => (string) $activity->id,
            'comentari' => $activity->comentari,
        ], 'OK');
    }

    /**
     * Elimina una activitat i, si existeix, el seu seguiment mirall.
     *
     * @param int|string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $activity = Activity::find($id);
        if ($activity === null) {
            return $this->sendNotFound('Activitat no trobada');
        }

        $seguimiento = $this->seguimientoService->findByActivityId((string) $activity->id);
        if ($seguimiento !== null) {
            $seguimiento->delete();
        }

        $activity->delete();

        return $this->sendResponse(['deleted' => true], 'OK');
    }

    /**
     * Manté el mirall en `seguimientos` per als dominis ja migrats.
     */
    private function syncSeguimientoMirror(Activity $activity): void
    {
        if (!in_array(class_basename((string) $activity->model_class), ['Colaboracion', 'Fct', 'AlumnoFct'], true)) {
            return;
        }

        $this->seguimientoService->syncFromActivity($activity);
    }
}
