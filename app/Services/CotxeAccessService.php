<?php

namespace Intranet\Services;



use Illuminate\Support\Facades\Log;
use Intranet\Entities\Cotxe;
use Intranet\Entities\CotxeAcces;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class CotxeAccessService
{


    /**
     * Retorna els segons que han passat des del darrer accés
     */
    public function recentAccessWithin(string $matricula, int $seconds): bool
    {
        $ultim = CotxeAcces::where('matricula', $matricula)
            ->latest('created_at')
            ->first();

        if (!$ultim) return false;

        return Carbon::parse($ultim->created_at)->diffInSeconds(now()) < $seconds;
    }

    /**
     * Registra un nou accés al pàrquing
     */
    public function registrarAcces(string $matricula, bool $autoritzat, bool $porta_oberta, string $device = null, string $tipus = null ): void
    {
        CotxeAcces::create([
            'matricula' => $matricula,
            'autoritzat' => $autoritzat,
            'porta_oberta' => $porta_oberta,
            'device' => $device,
            'tipus' => $tipus
        ]);


    }

    public function obrirIPorta(): void|null
    {
        
        return null;
        $url = config('parking.porta_url');
        $id = config('parking.porta_device_id');
        $user = config('parking.porta_user');
        $pass = config('parking.porta_pass');

        $response = Http::withBasicAuth($user, $pass)
            ->get("$url/api/callAction?deviceID=$id&name=turnOn");
        try {
            Log::info(print_r($response));
        } catch (\Exception $e) {

        }

        sleep(0.5);

        Http::withBasicAuth($user, $pass)
            ->get("$url/api/callAction?deviceID=$id&name=turnOff");
    }
}
