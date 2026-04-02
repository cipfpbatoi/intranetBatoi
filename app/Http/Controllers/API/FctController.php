<?php

namespace Intranet\Http\Controllers\API;


use Illuminate\Http\Request;
use Intranet\Application\Seguimiento\SeguimientoService;
use Intranet\Entities\Activity;
use Intranet\Entities\Fct;
use Intranet\Entities\Seguimiento;
use Intranet\Http\Resources\AlumnoFctControlResource;

class FctController extends ApiResourceController
{
        /**
         * @var string
         */
        protected $model = 'Fct';

        public function __construct(private readonly SeguimientoService $seguimientoService)
        {
            parent::__construct();
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
         * Registra un contacte telefònic sobre una FCT.
         *
         * @param int|string $id
         * @param Request $request
         * @return \Illuminate\Http\JsonResponse
         */
        public function telefon($id, Request $request)
        {
            $fct = Fct::find($id);
            if ($fct === null) {
                return $this->sendNotFound('Fct not found');
            }

            $activity = Activity::record(
                'phone',
                $fct,
                (string) $request->explicacion,
                null,
                'Seguiment telefònic'
            );

            $this->seguimientoService->record(
                $fct,
                'phone',
                'Seguiment telefònic',
                (string) $request->explicacion,
                ['source' => 'activities', 'activity_id' => $activity->id]
            );

            return $this->sendResponse($activity, 'OK');
        }

        /**
         * Retorna un contacte de FCT per al modal legacy.
         *
         * @param string $id
         * @return \Illuminate\Http\JsonResponse
         */
        public function showContact(string $id)
        {
            [$activity, $seguimiento] = $this->resolveContactRecord($id);

            if ($activity === null && $seguimiento === null) {
                return $this->sendNotFound('Contacte FCT not found');
            }

            return $this->sendResponse([
                'id' => (string) ($activity?->id ?? ('seguimiento-' . $seguimiento?->id)),
                'comentari' => $activity?->comentari ?? $seguimiento?->comment,
            ], 'OK');
        }

        /**
         * Actualitza un contacte de FCT existent sincronitzant la convivència temporal.
         *
         * @param string $id
         * @param Request $request
         * @return \Illuminate\Http\JsonResponse
         */
        public function updateContact(string $id, Request $request)
        {
            [$activity, $seguimiento] = $this->resolveContactRecord($id);

            if ($activity === null && $seguimiento === null) {
                return $this->sendNotFound('Contacte FCT not found');
            }

            $comentari = (string) $request->explicacion;

            if ($activity !== null) {
                $activity->comentari = $comentari;
                $activity->save();
                $seguimiento = $this->seguimientoService->syncFromActivity($activity);
            } elseif ($seguimiento !== null) {
                $seguimiento->comment = $comentari;
                $seguimiento->save();
            }

            return $this->sendResponse([
                'id' => (string) ($activity?->id ?? ('seguimiento-' . $seguimiento?->id)),
                'comentari' => $comentari,
            ], 'OK');
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

            $this->seguimientoService->record(
                $alumnoFct,
                'review',
                'Seguimiento Alumno',
                $request->explicacion,
                ['source' => 'activities', 'activity_id' => $activity->id]
            );

            return $this->sendResponse($activity, 'OK');

        }

        /**
         * @param string $id
         * @return array{0:?Activity,1:?Seguimiento}
         */
        private function resolveContactRecord(string $id): array
        {
            if (str_starts_with($id, 'seguimiento-')) {
                $seguimientoId = substr($id, strlen('seguimiento-'));
                $seguimiento = Seguimiento::query()
                    ->whereKey($seguimientoId)
                    ->where('domain_type', 'Fct')
                    ->first();

                return [null, $seguimiento];
            }

            $activity = Activity::query()
                ->whereKey($id)
                ->where('model_class', Fct::class)
                ->first();

            if ($activity === null) {
                return [null, null];
            }

            return [$activity, $this->seguimientoService->findByActivityId((string) $activity->id)];
        }



}
