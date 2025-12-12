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
        $log = Log::channel('parking');
        $url = config('parking.porta_url');
        $id = config('parking.porta_device_id');
        $user = config('parking.porta_user');
        $pass = config('parking.porta_pass');

        try {

            $offResponse = Http::withBasicAuth($user, $pass)
                ->get("$url/api/callAction", [
                    'deviceID' => $id,
                    'name' => 'turnOff',
                ]);
            sleep(0.5);

            $onResponse = Http::withBasicAuth($user, $pass)
                ->get("$url/api/callAction", [
                    'deviceID' => $id,
                    'name' => 'turnOn',
                ]);

            if (!$onResponse->successful()) {
                $log->error('Error obrint la porta (turnOn)', [
                    'status' => $onResponse->status(),
                    'reason' => $onResponse->reason(),
                    'user'  => $user,
                    'pass'  => $pass,
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
                $log->warning('Error tancant la porta (turnOff)', [
                    'status' => $offResponse->status(),
                    'reason' => $offResponse->reason(),
                    'body' => substr($offResponse->body(), 0, 500),
                    'url' => $url,
                    'deviceID' => $id,
                ]);
            }
            $log->info('Sennayls enviades correctament per obrir/tancar la porta');

            return $onResponse->successful();
        } catch (\Throwable $e) {
            $log->error('Excepció obrint la porta', ['message' => $e->getMessage()]);
            return false;
        }
    }
}
