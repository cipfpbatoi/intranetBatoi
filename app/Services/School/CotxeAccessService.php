<?php

namespace Intranet\Services\School;

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
     * Envia la petició per obrir la porta del pàrquing.
     *
     * @return bool True si la petició d'obertura ha sigut satisfactòria.
     */
    public function obrirIPorta(): bool
    {
        $log = Log::channel('parking');
        $url = config('parking.porta_url');
        $scene = config('parking.scene', 111);
        $user = config('parking.porta_user');
        $pass = config('parking.porta_pass');

        try {
            $endpoint = rtrim($url, '/').'/api/scenes/'.$scene.'/execute';
            $response = Http::withBasicAuth($user, $pass)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($endpoint, new \stdClass());

            if (!$response->successful()) {
                $log->error('Error obrint la porta mitjançant escena', [
                    'status' => $response->status(),
                    'reason' => $response->reason(),
                    'user' => $user,
                    'body' => substr((string) $response->body(), 0, 500),
                    'url' => $endpoint,
                ]);
                return false;
            }

            $log->info("S'ha enviat la petició d'obertura de la porta", [
                'status' => $response->status(),
                'url' => $endpoint,
            ]);

            return true;
        } catch (\Throwable $e) {
            $log->error('Excepció obrint la porta', ['message' => $e->getMessage()]);
            return false;
        }
    }
}
