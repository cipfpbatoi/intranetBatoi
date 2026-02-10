<?php


namespace Intranet\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Intranet\Entities\Cotxe;
use Intranet\Services\School\CotxeAccessService;
use Intranet\Services\HR\FitxatgeService;

enum Direccio: string { case Entrada = 'entrada'; case Eixida = 'eixida'; }

class CotxeController extends ApiResourceController
{
    protected  $model = 'Cotxe';

    public function __construct(
        private CotxeAccessService $access,
        private FitxatgeService    $fitxatge,
    ) {}

    public function eventEntrada(Request $request)
    {
        return $this->handleEvent($request, Direccio::Entrada);
    }

    public function obrirAutomatica(Request $request)
    {
        $requested = strtolower($request->input('direccio')
            ?? $request->input('direction')
            ?? $request->input('tipus')
            ?? Direccio::Entrada->value);

        $direccio = Direccio::tryFrom($requested) ?? Direccio::Entrada;

        return $this->handleEvent($request, $direccio);
    }

    public function eventSortida(Request $request)
    {
        return $this->handleEvent($request, Direccio::Eixida);
    }

    /**
     * Obertura manual per proves: no necessita matrícula.
     */
    public function obrirTest()
    {
        $oberta = $this->access->obrirIPorta();
         

        if (!$oberta) {
            return response()->json(['error' => 'No s\'ha pogut obrir la porta'], 500);
        }

        return response()->json(['status' => 'Porta oberta (test)']);
    }


    private function handleEvent(Request $request, Direccio $direccio)
    {
        $log = Log::channel('parking');
        [$matricula, $device] = $this->normalizePayload($request, $direccio);

        if (!$matricula) {
            return response()->json(['error' => 'Sense matrícula'], 422);
        }

        if ($this->access->recentAccessWithin($matricula, 30)) {
            //$log->alert("Accés $matricula recent");
            return response()->json(['status' => 'Accés massa recent']);
        }

        //$log->info("Accés {$direccio->value} | Matricula: {$matricula} | Dispositiu: {$device}");

        $cotxe = Cotxe::where('matricula', $matricula)->first();

        // Inicialitzem sempre per evitar "undefined variable"
        $autoritzat = false;
        $obrir      = false;

        // Regles d’obertura
        if ($cotxe) {
            // Matrícula exacta registrada → autoritzat i obrim
            $autoritzat = true;
            $obrir      = true;
        } elseif ($direccio === Direccio::Eixida && Cotxe::plateHamming1($matricula)->exists()) {
            // Eixida per coincidència Hamming-1 encara que no estiga autoritzat → obrim però marquem no autoritzat
            $autoritzat = false;
            $obrir      = true;
        }

        if ($obrir) {
            $obrir = $this->access->obrirIPorta();
            if (!$obrir) {
                $log->error("Error obrint la porta: resposta no satisfactòria");
            }
        }

        // Registre d’accés (sempre)
        if ($autoritzat){
            $this->access->registrarAcces(
            matricula:    $matricula,
            autoritzat:   $autoritzat,
            porta_oberta: $obrir,
            device:       $device,
            tipus:        $direccio->value,
            );
        }
        

        // Fitxatge: si vols que només fitxe en entrades, afegeix condició de direcció
        if ($cotxe?->professor /* && $direccio === Direccio::Entrada */) {
            $this->fitxatge->fitxar($cotxe->professor->dni);
        }

        $msg = $obrir
            ? "Porta oberta ({$direccio->value})"
            : ($cotxe ? 'No autoritzat' : 'No autoritzat');

        $log->info("Resultat accés {$direccio->value} | Matricula: {$matricula} | {$msg}");

        return response()->json(['status' => $msg]);
    }


    /**
     * Accepta payloads heterogenis (Milesight, etc.)
     * - Entrada:   plate / device
     * - Eixida:    license_plate / device_name
     */
    private function normalizePayload(Request $request, Direccio $direccio): array
    {
        $data = json_decode($request->getContent() ?: '{}', true) ?: [];

        // Clau matrícula possibles
        $rawPlate = $data['plate']           ??  // alguns hooks d’entrada
            $data['license_plate']   ??  // eixida Milesight
            $data['licensePlate']    ??  // variant camelCase
            $data['matricula']       ??  // peticions internes
            '';

        // Clau device possibles
        $rawDevice = $data['device']         ??  // alguns hooks
            $data['device_name']    ??  // Milesight
            null;

        $matricula = strtoupper(preg_replace('/\s+/', '', $rawPlate));
        $device    = $rawDevice ?: ($direccio === Direccio::Entrada ? 'Cam_exterior' : 'Cam_interior');

        return [$matricula, $device];
    }
}
