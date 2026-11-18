<?php

namespace Intranet\Services\School;

use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
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
     * Reserva atòmicament una passada de professor i sentit per a deduplicar-la.
     *
     * @param string $professor Identificador intern del professor.
     * @param string $tipus     Sentit de la passada.
     * @param int    $seconds   Duració de la finestra de deduplicació.
     *
     * @return bool True quan la passada queda reservada.
     */
    public function reservarAcces(string $professor, string $tipus, int $seconds): bool
    {
        try {
            return DB::transaction(function () use ($professor, $tipus, $seconds): bool {
                $reserva = DB::table('cotxe_acces_reserves')
                    ->where('idProfesor', $professor)
                    ->where('tipus', $tipus)
                    ->lockForUpdate()
                    ->first();

                if ($reserva && Carbon::parse($reserva->expires_at)->isFuture()) {
                    return false;
                }

                $valors = [
                    'expires_at' => now()->addSeconds($seconds),
                    'updated_at' => now(),
                ];

                if ($reserva) {
                    DB::table('cotxe_acces_reserves')->where('id', $reserva->id)->update($valors);
                } else {
                    DB::table('cotxe_acces_reserves')->insert($valors + [
                        'idProfesor' => $professor,
                        'tipus' => $tipus,
                        'created_at' => now(),
                    ]);
                }

                return true;
            });
        } catch (QueryException) {
            return false;
        }
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
    public function registrarAcces(
        string $matricula,
        bool $autoritzat,
        bool $porta_oberta,
        ?string $device = null,
        ?string $tipus = null,
        ?string $professor = null,
        ?string $matriculaCanonica = null,
        string $resultatResolucio = 'sense_candidat',
        ?int $diferenciaOcr = null,
        int $intentsDomotica = 0,
        ?string $errorDomotica = null,
    ): void
    {
        CotxeAcces::create([
            'matricula' => $matricula,
            'autoritzat' => $autoritzat,
            'porta_oberta' => $porta_oberta,
            'device' => $device,
            'tipus' => $tipus,
            'idProfesor' => $professor,
            'matricula_canonica' => $matriculaCanonica,
            'resultat_resolucio' => $resultatResolucio,
            'diferencia_ocr' => $diferenciaOcr,
            'intents_domotica' => $intentsDomotica,
            'error_domotica' => $errorDomotica,
        ]);
    }

    /**
     * Envia com a màxim dos ordres a la domòtica per a una mateixa passada.
     *
     * @return array{porta_oberta: bool, intents: int, error: string|null}
     */
    public function obrirIPortaAmbReintent(): array
    {
        if ($this->obrirIPorta()) {
            return ['porta_oberta' => true, 'intents' => 1, 'error' => null];
        }

        if ($this->obrirIPorta()) {
            return ['porta_oberta' => true, 'intents' => 2, 'error' => null];
        }

        return ['porta_oberta' => false, 'intents' => 2, 'error' => 'sense_resposta_satisfactoria'];
    }

    /**
     * Envia la petició per obrir la porta del pàrquing mitjançant la escena domòtica.
     *
     * @return bool True si la petició d'obertura ha sigut satisfactòria.
     */
    public function obrirIPorta(): bool
    {
        $log = Log::channel('parking');
        $url = config('parking.porta_url');
        if (empty($url)) {
            $url = config('variables.domotica.host', 'http://172.16.10.74');
        }

        $scene = config('parking.porta_scene_id', config('parking.scene', 111));
        if (empty($scene)) {
            $scene = config('variables.domotica.openSceneId', 111);
        }

        $user = config('parking.porta_user', config('variables.domotica.user', 'api'));
        $pass = config('parking.porta_pass', config('variables.domotica.pass', 'Api*HC3*Batoi26'));

        $endpoint = rtrim((string) $url, '/').'/api/scenes/'.$scene.'/execute';

        try {
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
