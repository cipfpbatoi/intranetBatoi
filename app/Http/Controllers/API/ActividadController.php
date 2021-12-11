<?php

namespace Intranet\Http\Controllers\API;


use Illuminate\Http\Request;
use Intranet\Entities\Actividad;
use Styde\Html\Facades\Alert;

class ActividadController extends ApiBaseController
{

    protected $model = 'Actividad';


    public function getFiles($id){
        $actividad = Actividad::find($id);
        $path = storage_path().'/app/public/Extraescolars/';
        $data = [];
        for ($i=1;$i<4;$i++){
            $key = 'image'.$i;
            if ($actividad->$key) {
                $data[$i]['name'] = $actividad->$key;
                try{
                    $data[$i]['size'] = filesize($path.$actividad->$key);
                } catch (\Exception $e){
                    $data[$i]['size'] = 9999;
                }
                $data[$i]['accepted'] = true;
            }
        }
        return $this->sendResponse($data, 'OK');
    }
}
