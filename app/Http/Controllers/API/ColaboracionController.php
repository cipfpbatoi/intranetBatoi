<?php

namespace Intranet\Http\Controllers\API;

use Illuminate\Http\Request;
use Intranet\Http\Requests;
use Intranet\Http\Controllers\Controller;
use Intranet\Http\Controllers\API\ApiBaseController;
use Intranet\Entities\Colaboracion;
use Intranet\Entities\Profesor;
use Intranet\Entities\Activity;
use Intranet\Entities\Fct;
use Intranet\Http\Resources\ColaboracionResource;

class ColaboracionController extends ApiBaseController
{

    protected $model = 'Colaboracion';

    public function instructores($id){
        $colaboracion = Colaboracion::find($id);
        $data = $colaboracion->Centro->instructores;
        return $this->sendResponse($data, 'OK');
    }

    public function resolve($id){
        $colaboracion = Colaboracion::find($id);
        $colaboracion::resolve($id);
        return $this->sendResponse($colaboracion,'OK');
    }

    public function refuse($id){
        $colaboracion = Colaboracion::find($id);
        $colaboracion::refuse($id);
        return $this->sendResponse($colaboracion,'OK');
    }
    public function unauthorize($id){
        $colaboracion = Colaboracion::find($id);
        $colaboracion::putEstado($id,1);
        return $this->sendResponse($colaboracion,'OK');
    }
    public function switch($id){
        $colaboracion = Colaboracion::find($id);
        $profesor = Profesor::where('api_token',$_GET['api_token'])->first();
        $colaboracion->tutor = $profesor->dni;
        $colaboracion->save();
        return $this->sendResponse($profesor,'OK');
    }
    public function telefon($id,Request $request){
        $activities = Activity::where('action','phone')->where('model_id',$id)->get();
        foreach ($activities as $activity)
            if (esMismoDia($activity->created_at,Hoy())){
                $activity->comentari = $request->explicacion;
                $activity->save();
                return $this->sendError($activity,'404');
            }
        $activity = Activity::record('phone', Fct::find($id),$request->explicacion,null,'Seguiment telefònic');
        return $this->sendResponse($activity,'OK');
    }
    public function info($dni){
        return ColaboracionResource::collection($this->selectColaboraciones($dni,2,config('fctEmails.request')));
    }

    private function selectColaboraciones($dni,$estado,$document=null){
        $colaboraciones = Colaboracion::MiColaboracion(null,$dni)->where('tutor',$dni)->where('estado',$estado)->get();
        if (!$document) {
            return $colaboraciones;
        }
        $noEnviadas = collect();
        foreach ($colaboraciones as $colaboracion){
            if (Activity::where('model_class','Intranet\Entities\Colaboracion')->where('model_id',$colaboracion->id)->where('document','=',$document['subject'])->count() == 0){
                if ($document['fcts'] && count($colaboracion->Fcts)) {
                    $noEnviadas->push($colaboracion);
                }
                if (!$document['fcts'] && count($colaboracion->Fcts) == 0) {
                    $noEnviadas->push($colaboracion);
                }
            }

        }
        return $noEnviadas;
    }



}
