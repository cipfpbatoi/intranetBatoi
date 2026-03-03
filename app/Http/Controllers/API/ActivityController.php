<?php

namespace Intranet\Http\Controllers\API;


use Intranet\Entities\Activity;
use Intranet\Exceptions\NotFoundDomainException;

/**
 * Controlador API d'activitats.
 */
class ActivityController extends ApiResourceController
{

    protected $model = 'Activity';
    

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
            $activity2->save();
            return $this->sendResponse(['id'=>$activity2->id], 'OK');
        }


    }
}
