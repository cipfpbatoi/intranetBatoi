<?php

namespace Intranet\Http\Controllers\API;


use Intranet\Services\CotxeAccessService;
use Illuminate\Http\Request;
use Intranet\Entities\Cotxe;



class CotxeController extends ApiResourceController
{

    protected $model = 'Cotxe';


    public function eventEntrada(Request $request, CotxeAccessService $accessService)
    {
        $data = json_decode($request->getContent(), true);
        $matricula = strtoupper($data['plate'] ?? '');
        $device = $data['device'] ?? 'Cam_exterior';

        if (!$matricula) return response()->json(['error' => 'Sense matrícula']);

        $cotxe = Cotxe::where('matricula', $matricula)->first();
        if (!$cotxe){
            $accessService->registrarAcces($matricula, false, false, $device,'entrada');
            return response()->json(['status' => 'No autoritzat']);
        }

        $accessService->obrirIPorta();
        $accessService->registrarAcces($matricula, true, true, $device,'entrada');

        return response()->json(['status' => 'Porta oberta (entrada)']);
    }


    public function eventSortida(Request $request, CotxeAccessService $accessService)
    {
        $data = json_decode($request->getContent(), true);
        $matricula = strtoupper($data['license_plate'] ?? '');
        $device = $data['device_name'] ?? 'Cam_interior';

        if (!$matricula) return response()->json(['error' => 'Sense matrícula']);

        if (!$accessService->estaDins($matricula)) {
            return response()->json(['status' => 'El cotxe no consta com a dins']);
        }

        if ($accessService->segonsDesdeUltimAcces($matricula) < 60) {
            return response()->json(['status' => 'Accés massa recent']);
        }

        $accessService->obrirIPorta();
        $accessService->registrarAcces($matricula, true, true, $device, 'sortida');

        return response()->json(['status' => 'Porta oberta (sortida)']);
    }

}
