<?php

namespace Intranet\Http\Controllers\API;


use Intranet\Entities\Actividad;
use Illuminate\Support\Facades\Log;


class ActividadController extends ApiResourceController
{

    protected $model = 'Actividad';


    public function getFiles($id)
    {
        $actividad = Actividad::find($id);
        $path = storage_path().'/app/public/Extraescolars/';
        $data = [];
        for ($i = 1; $i < 4; $i++) {
            $key = 'image' . $i;
            if ($actividad->$key) {
                $data[$i]['name'] = $actividad->$key;
                try {
                    $data[$i]['size'] = filesize($path . $actividad->$key);
                } catch (\Exception $e) {
                    report($e);
                    Log::warning('Error obtenint mida d\'arxiu d\'activitat extraescolar.', [
                        'actividad_id' => $id,
                        'file' => $actividad->$key,
                        'error' => $e->getMessage(),
                    ]);
                    $data[$i]['size'] = 9999;
                }
                $data[$i]['accepted'] = true;
            }
        }
        return $this->sendResponse($data, 'OK');
    }
}
