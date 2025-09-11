<?php


namespace Intranet\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Intranet\Entities\Cotxe;
use Intranet\Services\CotxeAccessService;
use Intranet\Services\FitxatgeService;

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

    public function eventSortida(Request $request)
    {
        return $this->handleEvent($request, Direccio::Eixida);
    }

    private function handleEvent(Request $request, Direccio $direccio)
    {
        [$matricula, $device] = $this->normalizePayload($request, $direccio);

        if (!$matricula) {
            return response()->json(['error' => 'Sense matrícula'], 422);
        }

        if ($this->access->recentAccessWithin($matricula, 30)) {
            return response()->json(['status' => 'Accés massa recent']);
        }

        Log::info("Accés {$direccio->value} | Matricula: {$matricula} | Dispositiu: {$device}");

        $cotxe = Cotxe::where('matricula', $matricula)->first();

        // Regles d’obertura
        $autoritzat = false;
        $obrir      = false;

        if ($cotxe ) {
            $autoritzat = true;
            $obrir = true;
        } elseif ($direccio === Direccio::Eixida && Cotxe::plateHamming1($matricula)->exists()) {
            // Permet eixida amb coincidència Hamming-1 encara que no estiga autoritzat
            $autoritzat = false;
            $obrir = true;
        }

        if ($obrir) {
            $this->access->obrirIPorta();
        }

        $this->access->registrarAcces(
            matricula:    $matricula,
            autoritzat:   $autoritzat,
            porta_oberta: $obrir,
            device:       $device,
            tipus:        $direccio->value
        );

        // Fitxatge si hi ha professor vinculat
        if ($cotxe?->professor) {
            $this->fitxatge->fitxar($cotxe->professor->dni);
        }

        $msg = $obrir
            ? "Porta oberta ({$direccio->value})"
            : ($cotxe ? 'No autoritzat' : 'No autoritzat');

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

        $matricula = strtoupper(trim($rawPlate));
        $device    = $rawDevice ?: ($direccio === Direccio::Entrada ? 'Cam_exterior' : 'Cam_interior');

        return [$matricula, $device];
    }
}
