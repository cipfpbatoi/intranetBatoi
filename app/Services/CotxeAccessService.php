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

    public function obrirIPorta(): bool
    {
        $url = config('parking.porta_url');
        $id = config('parking.porta_device_id');
        $user = config('parking.porta_user');
        $pass = config('parking.porta_pass');

        try {
            $onResponse = Http::withBasicAuth($user, $pass)
                ->get("$url/api/callAction", [
                    'deviceID' => $id,
                    'name' => 'turnOn',
                ]);

            if (!$onResponse->successful()) {
                Log::error('Error obrint la porta (turnOn)', [
                    'status' => $onResponse->status(),
                    'reason' => $onResponse->reason(),
                    'body' => substr($onResponse->body(), 0, 500),
                    'url' => $url,
                    'deviceID' => $id,
                ]);
                return false;
            }

            sleep(0.5);

            // Intentem apagar encara que l'obertura haja fallat
            $offResponse = Http::withBasicAuth($user, $pass)
                ->get("$url/api/callAction", [
                    'deviceID' => $id,
                    'name' => 'turnOff',
                ]);

            if (!$offResponse->successful()) {
                Log::warning('Error tancant la porta (turnOff)', [
                    'status' => $offResponse->status(),
                    'reason' => $offResponse->reason(),
                    'body' => substr($offResponse->body(), 0, 500),
                    'url' => $url,
                    'deviceID' => $id,
                ]);
            }

            return true;
        } catch (\Throwable $e) {
            Log::error('Excepció obrint la porta', ['message' => $e->getMessage()]);
            return false;
        }
    }
}
