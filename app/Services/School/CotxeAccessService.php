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
     * Envia la senyal per obrir la porta de l'aparcament mitjançant la escena domòtica.
     *
     * @return bool True si la sol·licitud d'obertura ha sigut satisfactòria.
     */
    public function obrirIPorta(): bool
    {
        $log = Log::channel('parking');
        $user = config('variables.domotica.user');
        $pass = config('variables.domotica.pass');
        $sceneId = (int) config('variables.domotica.openSceneId', 111);
        $url = rtrim((string) config('variables.domotica.host', 'http://172.16.10.74'), '/').'/api/scenes/'.$sceneId.'/execute';

        try {

            $response = Http::withBasicAuth($user, $pass)
                ->accept('application/json')
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($url, []);

            if (!$response->successful()) {
                $log->error('Error obrint la porta (scene)', [
                    'status' => $response->status(),
                    'reason' => $response->reason(),
                    'user'  => $user,
                    'pass'  => $pass,
                    'body' => substr($response->body(), 0, 500),
                    'url' => $url,
                ]);
                return false;
            }
            $log->info('S'ha enviat la senyal d''obertura de porta');
            return $response->successful();
        } catch (\Throwable $e) {
            $log->error('Excepció obrint la porta', ['message' => $e->getMessage()]);
            return false;
        }
    }
}
