<?php

namespace Intranet\Http\Controllers\API;


use Illuminate\Http\Request;
use Intranet\Application\Profesor\ProfesorService;
use Intranet\Entities\Activity;
use Intranet\Entities\Fct;
use Intranet\Http\Resources\AlumnoFctControlResource;

class FctController extends ApiResourceController
{
        private ?ProfesorService $profesorService = null;

        private function profesores(): ProfesorService
        {
            if ($this->profesorService === null) {
                $this->profesorService = app(ProfesorService::class);
            }

            return $this->profesorService;
        }

        public function llist($id)
        {

            $fct = Fct::find($id);
            $data = AlumnoFctControlResource::collection($fct->AlFct);

            return $this->sendResponse($data, 'OK');
        }

        public function seguimiento($id,Request $request)
        {
            $user = $request->user('sanctum') ?? $request->user('api');
            if ($user === null) {
                $user = $this->profesores()->findByApiToken((string) ($request->query('api_token') ?? $request->input('api_token') ?? ''));
            }
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
