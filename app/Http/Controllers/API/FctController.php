<?php

namespace Intranet\Http\Controllers\API;


use Illuminate\Http\Request;
use Intranet\Entities\Activity;
use Intranet\Entities\Fct;
use Intranet\Http\Resources\AlumnoFctControlResource;

class FctController extends ApiResourceController
{
        /**
         * Registra o actualitza el seguiment telefònic d'una FCT.
         *
         * @param int|string $id
         * @param Request $request
         * @return \Illuminate\Http\JsonResponse
         */
        public function telefonico($id, Request $request)
        {
            $fct = Fct::find($id);
            if ($fct === null) {
                return $this->sendNotFound('FCT not found');
            }

            $activity = $this->upsertDailyActivity(
                'phone',
                $id,
                (string) $request->explicacion,
                static fn () => $fct,
                'Seguiment telefònic'
            );

            return $this->sendResponse($activity, 'OK');
        }

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

        /**
         * Crea o actualitza una activitat única per dia per al model indicat.
         */
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
