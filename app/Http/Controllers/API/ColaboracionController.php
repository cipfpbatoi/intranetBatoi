<?php

namespace Intranet\Http\Controllers\API;

use Illuminate\Http\Request;
use Intranet\Entities\Colaboracion;
use Intranet\Entities\Profesor;
use Intranet\Entities\Activity;
use Intranet\Entities\Fct;
use Intranet\Services\StateService;


class ColaboracionController extends ApiBaseController
{

    protected $model = 'Colaboracion';

    public function instructores($id)
    {
        $colaboracion = Colaboracion::find($id);
        $data = isset($colaboracion->Centro)
            ?$colaboracion->Centro->instructores->sortBy('surnames')
            :[];
        return $this->sendResponse($data, 'OK');
    }

    public function resolve($id)
    {
        $colaboracion = Colaboracion::find($id);
        $staSer = new StateService($colaboracion);
        $staSer->resolve();
        return $this->sendResponse($colaboracion, 'OK');
    }

    public function refuse($id)
    {
        $colaboracion = Colaboracion::find($id);
        $staSer = new StateService($colaboracion);
        $staSer->refuse();
        return $this->sendResponse($colaboracion, 'OK');
    }
    public function unauthorize($id)
    {
        $colaboracion = Colaboracion::find($id);
        $staSer = new StateService($colaboracion);
        $staSer->putEstado(1);
        return $this->sendResponse($colaboracion, 'OK');
    }
    public function switch($id)
    {
        $colaboracion = Colaboracion::find($id);
        $profesor = Profesor::where('api_token', $_GET['api_token'])->first();
        $colaboracion->tutor = $profesor->dni;
        $colaboracion->save();
        return $this->sendResponse($profesor, 'OK');
    }

    public function telefon($id, Request $request)
    {
        $activities = Activity::where('action', 'phone')->where('model_id', $id)->get();
        foreach ($activities as $activity) {
            if (esMismoDia($activity->created_at, Hoy())) {
                $activity->comentari = $request->explicacion;
                $activity->save();
                return $this->sendResponse($activity, 'OK');
            }
        }
        $activity = Activity::record(
            'phone',
            Fct::find($id),
            $request->explicacion,
            null,
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
        $activities = Activity::where('action', 'book')->where('model_id', $id)->get();
        foreach ($activities as $activity) {
            if (esMismoDia($activity->created_at, Hoy())) {
                $activity->comentari = $request->explicacion;
                $activity->save();
                return $this->sendResponse($activity, 'OK');
            }
        }
        $activity = Activity::record(
            'book',
            Colaboracion::find($id),
            $request->explicacion,
            null,
            'Contacte previ'
        );
        return $this->sendResponse($activity, 'OK');
    }

}
