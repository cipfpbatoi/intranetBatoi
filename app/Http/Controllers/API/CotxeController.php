<?php

namespace Intranet\Http\Controllers\API;


use Illuminate\Support\Facades\Log;
use Intranet\Services\CotxeAccessService;
use Illuminate\Http\Request;
use Intranet\Entities\Cotxe;
use Intranet\Services\FitxatgeService;


class CotxeController extends ApiResourceController
{

    protected $model = 'Cotxe';


    public function eventEntrada(Request $request, CotxeAccessService $accessService, FitxatgeService $fitxatgeService)
    {
        $data = json_decode($request->getContent(), true);
        $matricula = strtoupper($data['plate'] ?? '');
        $device = $data['device'] ?? 'Cam_exterior';
        Log::info("Matricula detectada: $matricula, Dispositiu: $device");

        if (!$matricula) return response()->json(['error' => 'Sense matrícula']);

        $cotxe = Cotxe::where('matricula', $matricula)->first();
        if (!$cotxe){
            $accessService->registrarAcces($matricula, false, false, $device,'entrada');
            return response()->json(['status' => 'No autoritzat']);
        }

        $accessService->obrirIPorta();
        $accessService->registrarAcces($matricula, true, true, $device,'entrada');

        if ($cotxe->professor) {
            $fitxatgeService->fitxar($cotxe->professor->dni);
        }


        return response()->json(['status' => 'Porta oberta (entrada)']);
    }


    public function eventSortida(Request $request, CotxeAccessService $accessService, FitxatgeService $fitxatgeService)
    {
        $data = json_decode($request->getContent(), true);
        $matricula = strtoupper($data['license_plate'] ?? '');
        $device = $data['device_name'] ?? 'Cam_exterior';
        Log::info("Matricula detectada: $matricula, Dispositiu: $device");

        //if (!$matricula) return response()->json(['error' => 'Sense matrícula']);
        /*
        if ($accessService->segonsDesdeUltimAcces($matricula) < 60) {
            return response()->json(['status' => 'Accés massa recent']);
        }
        */

        $cotxe = Cotxe::where('matricula', $matricula)->first();
        if ($cotxe->professor) {
            $accessService->obrirIPorta();
            $accessService->registrarAcces($matricula, true, true, $device, 'sortida');
            $fitxatgeService->fitxar($cotxe->professor->dni);
        } elseif (Cotxe::plateHamming1($matricula)->exists()) {
            $accessService->obrirIPorta();
            $accessService->registrarAcces($matricula, true, true, $device, 'sortida');
        }



        return response()->json(['status' => 'Porta oberta (sortida)']);
    }

}
