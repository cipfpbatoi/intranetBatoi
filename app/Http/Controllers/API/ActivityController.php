<?php

namespace Intranet\Http\Controllers\API;


use Intranet\Entities\Activity;
use Intranet\Entities\Fct;

class ActivityController extends ApiBaseController
{

    protected $model = 'Activity';
    

    public function move($id,$fct){
        $activity = Activity::findOrFail($id);
        $oldFct = Fct::findOrFail($activity->model_id);
        $newFct = Fct::findOrFail($fct);
        if ($oldFct->idColaboracion == $newFct->idColaboracion){
            $activity->model_id = $fct;
            $activity->save();
            return $this->sendResponse($activity, 'OK');
        } else {
            return $this->sendFail("Col.laboracions diferents");
        }

    }
}
