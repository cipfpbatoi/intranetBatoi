<?php

namespace Intranet\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Intranet\Entities\CotxeAcces;

class CotxeAccessService
{
    /**
     * Servei que encapsula la lògica d'accés al pàrquing:
     * comprova accessos recents, registra nous intents i envia
     * l'ordre d'obrir la porta al dispositiu configurat.
     */
    /**
     * Comprova si hi ha hagut un accés recent d'una matrícula.
     *
     * @param string $matricula Matrícula del vehicle.
     * @param int    $seconds   Llindar en segons per considerar l'accés recent.
     *
     * @return bool True si existeix un accés dins del rang indicat.
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
     * Registra un nou accés al pàrquing.
     *
     * @param string      $matricula    Matrícula del vehicle.
     * @param bool        $autoritzat   Si l'accés és autoritzat.
     * @param bool        $porta_oberta Si la porta s'ha obert.
     * @param string|null $device       Identificador del dispositiu d'accés.
     * @param string|null $tipus        Tipus o origen de l'accés.
     */
    public function registrarAcces(string $matricula, bool $autoritzat, bool $porta_oberta, string $device = null, string $tipus = null): void
    {
        CotxeAcces::create([
            'matricula' => $matricula,
            'autoritzat' => $autoritzat,
            'porta_oberta' => $porta_oberta,
            'device' => $device,
            'tipus' => $tipus
        ]);
    }

    /**
     * Envia les ordres d'obrir i tancar la porta al dispositiu IoT.
     *
     * @return bool True si la sol·licitud d'obertura ha sigut satisfactòria.
     */
    public function obrirIPorta(): bool
    {
        $log = Log::channel('parking');
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
