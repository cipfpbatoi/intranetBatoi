<?php

namespace Intranet\Http\Controllers\API;


use Intranet\Entities\Actividad;
use Intranet\Http\Resources\ActividadEditResource;
use Illuminate\Support\Facades\Log;

/**
 * Controlador API per a activitats.
 */
class ActividadController extends ApiResourceController
{
    /**
     * Model Eloquent gestionat per l'endpoint.
     *
     * @var string
     */
    protected $model = 'Actividad';
    /**
     * Recurs específic per al payload d'edició.
     *
     * @var class-string<ActividadEditResource>
     */
    protected $editResource = ActividadEditResource::class;

    /**
     * Retorna la metainformació d'arxius vinculats a l'activitat.
     *
     * @param int|string $id
     * @return \Illuminate\Http\JsonResponse
     */
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
