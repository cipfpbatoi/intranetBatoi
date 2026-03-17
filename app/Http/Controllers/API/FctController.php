<?php

namespace Intranet\Http\Controllers\API;


use Illuminate\Http\Request;
use Intranet\Entities\Activity;
use Intranet\Entities\Fct;
use Intranet\Http\Resources\AlumnoFctControlResource;

class FctController extends ApiResourceController
{
        /**
         * Retorna l'alumnat associat a una FCT.
         *
         * @param int|string $id
         * @return \Illuminate\Http\JsonResponse
         */
        public function llist($id)
        {
            $fct = Fct::find($id);
            if ($fct === null) {
                return $this->sendNotFound('FCT not found');
            }

            $data = AlumnoFctControlResource::collection($fct->AlFct);

            return $this->sendResponse($data, 'OK');
        }

        /**
         * Registra un seguiment manual sobre un alumne en FCT.
         *
         * @param int|string $id
         * @param Request $request
         * @return \Illuminate\Http\JsonResponse
         */
        public function seguimiento($id,Request $request)
        {
            $alumnoFct = \Intranet\Entities\AlumnoFct::find($id);
            if ($alumnoFct === null) {
                return $this->sendNotFound('AlumnoFct not found');
            }

            $user = $request->user()
                ?? $request->user('web')
                ?? $request->user('profesor')
                ?? $request->user('sanctum')
                ?? $request->user('api');

            if ($user === null) {
                return $this->sendError('Unauthorized', 401);
            }

            $activity = new Activity();
            $activity->model_id = $id;
            $activity->action = 'review';
            $activity->model_class = 'Intranet\Entities\AlumnoFct';
            $activity->author_id = $user->dni;
            $activity->document = 'Seguimiento Alumno';
            $activity->comentari = $request->explicacion;
            $activity->save();
            return $this->sendResponse($activity, 'OK');

        }



}
