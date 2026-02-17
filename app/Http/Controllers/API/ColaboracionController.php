<?php

namespace Intranet\Http\Controllers\API;

use Illuminate\Http\Request;
use Intranet\Application\Profesor\ProfesorService;
use Intranet\Entities\Activity;
use Intranet\Entities\Colaboracion;
use Intranet\Entities\Fct;
use Intranet\Services\General\StateService;


class ColaboracionController extends ApiBaseController
{

    protected $model = 'Colaboracion';
    private ?ProfesorService $profesorService = null;

    private function profesores(): ProfesorService
    {
        if ($this->profesorService === null) {
            $this->profesorService = app(ProfesorService::class);
        }

        return $this->profesorService;
    }

    public function instructores($id)
    {
        $colaboracion = Colaboracion::find($id);
        if ($colaboracion === null) {
            return $this->sendFail(['success' => false, 'message' => 'Colaboració no trobada.'], 404);
        }

        $data = isset($colaboracion->Centro)
            ?$colaboracion->Centro->instructores->sortBy('surnames')
            :[];
        return $this->sendResponse($data, 'OK');
    }

    public function resolve($id)
    {
        return $this->changeState($id, 'resolve');
    }

    public function refuse($id)
    {
        return $this->changeState($id, 'refuse');
    }

    public function unauthorize($id)
    {
        $colaboracion = Colaboracion::find($id);
        if ($colaboracion === null) {
            return $this->sendFail(['success' => false, 'message' => 'Colaboració no trobada.'], 404);
        }

        $staSer = new StateService($colaboracion);
        $staSer->putEstado(1);
        return $this->sendResponse($colaboracion, 'OK');
    }

    public function switch($id)
    {
        $colaboracion = Colaboracion::find($id);
        if ($colaboracion === null) {
            return $this->sendFail(['success' => false, 'message' => 'Colaboració no trobada.'], 404);
        }

        $profesor = $this->profesores()->findByApiToken((string) request()->query('api_token', ''));
        if ($profesor === null) {
            return $this->sendFail(['success' => false, 'message' => 'Professor no trobat.'], 404);
        }

        $colaboracion->tutor = $profesor->dni;
        $colaboracion->save();

        return $this->sendResponse($profesor, 'OK');
    }

    public function telefon($id, Request $request)
    {
        $activity = $this->upsertDailyActivity(
            'phone',
            $id,
            (string) $request->explicacion,
            static fn (string $resourceId) => Fct::find($resourceId),
            'Seguiment telefònic'
        );

        return $this->sendResponse($activity, 'OK');
    }

    public function alumnat($id, Request $request)
    {
        $activity = Activity::record(
            'review',
            Fct::find($id),
            $request->explicacion,
            null,
            'Seguiment Alumnat'
        );

        return $this->sendResponse($activity, 'OK');
    }

    public function book($id, Request $request)
    {
        $activity = $this->upsertDailyActivity(
            'book',
            $id,
            (string) $request->explicacion,
            static fn (string $resourceId) => Colaboracion::find($resourceId),
            'Contacte previ'
        );

        return $this->sendResponse($activity, 'OK');
    }

    private function changeState(string|int $id, string $action)
    {
        $colaboracion = Colaboracion::find($id);
        if ($colaboracion === null) {
            return $this->sendFail(['success' => false, 'message' => 'Colaboració no trobada.'], 404);
        }

        $stateService = new StateService($colaboracion);
        $stateService->{$action}();

        return $this->sendResponse($colaboracion, 'OK');
    }

    private function upsertDailyActivity(
        string $action,
        string|int $modelId,
        string $explicacion,
        callable $modelResolver,
        string $document
    ): Activity {
        $activities = Activity::query()
            ->where('action', $action)
            ->where('model_id', $modelId)
            ->get();

        foreach ($activities as $activity) {
            if (esMismoDia($activity->created_at, Hoy())) {
                $activity->comentari = $explicacion;
                $activity->save();

                return $activity;
            }
        }

        $model = $modelResolver((string) $modelId);

        return Activity::record(
            $action,
            $model,
            $explicacion,
            null,
            $document
        );
    }
}
