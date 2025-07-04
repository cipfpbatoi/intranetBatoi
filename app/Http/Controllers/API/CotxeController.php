<?php

namespace Intranet\Http\Controllers\API;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intranet\Entities\Cotxe;
use Intranet\Entities\CotxeAcces;


class CotxeController extends ApiResourceController
{

    protected $model = 'Cotxe';

    public function eventPorta(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        if (!$data) return response()->json(['error' => 'Format incorrecte'], 400);

        $matricula = strtoupper($data['plate']);
        $hora = $data['time'] ?? now();
        $device = $data['device'] ?? null;

        $cotxe = Cotxe::where('matricula', $matricula)->first();
        $autoritzat = $cotxe !== null;

        CotxeAcces::create([
            'matricula' => $matricula,
            'data' => $hora,
            'autoritzat' => $autoritzat,
            'porta_oberta' => false,
            'device' => $device,
            'image_path' => null,
        ]);

        if ($autoritzat) {
            Http::withBasicAuth('api', 'Admin*HC3*Batoi22')
                ->get('http://195.181.255.163:8898/api/callAction?deviceID=267&name=turnOn');

            CotxeAcces::where('matricula', $matricula)->latest()->first()->update([
                'porta_oberta' => true
            ]);

            Http::withBasicAuth('api', 'Admin*HC3*Batoi22')
                ->get('http://195.181.255.163:8898/api/callAction?deviceID=267&name=turnOff');
        }

        return response()->json(['status' => 'OK']);
    }



}
