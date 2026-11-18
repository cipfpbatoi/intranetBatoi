<?php


namespace Intranet\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Intranet\Entities\Cotxe;
use Intranet\Services\School\CotxeAccessService;
use Intranet\Services\HR\FitxatgeService;

enum Direccio: string { case Entrada = 'entrada'; case Eixida = 'eixida'; }

/**
 * Gestiona els esdeveniments de les càmeres i l'obertura de l'aparcament.
 */
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

        $resolucio = Cotxe::resoldreLecturaAcces($matricula);
        $professor = $resolucio['professor'];

        if (!$professor) {
            $this->access->registrarAcces(
                matricula: $matricula,
                autoritzat: false,
                porta_oberta: false,
                device: $device,
                tipus: $direccio->value,
                resultatResolucio: $resolucio['resultat'],
                diferenciaOcr: $resolucio['diferencia_ocr'],
            );

            $log->info('Accés al pàrquing no autoritzat', [
                'sentit' => $direccio->value,
                'resultat' => $resolucio['resultat'],
            ]);

            return response()->json(['status' => 'No autoritzat']);
        }

        if (!$this->access->reservarAcces($professor, $direccio->value)) {
            $log->info('Accés al pàrquing descartat per duplicat', [
                'sentit' => $direccio->value,
                'professor' => $professor,
            ]);

            return response()->json(['status' => 'Accés massa recent']);
        }

        try {
            $obertura = $this->access->obrirIPortaAmbReintent();
            $this->access->registrarAcces(
                matricula: $matricula,
                autoritzat: true,
                porta_oberta: $obertura['porta_oberta'],
                device: $device,
                tipus: $direccio->value,
                professor: $professor,
                matriculaCanonica: $resolucio['matricula_canonica'],
                resultatResolucio: $resolucio['resultat'],
                diferenciaOcr: $resolucio['diferencia_ocr'],
                intentsDomotica: $obertura['intents'],
                errorDomotica: $obertura['error'],
            );

            $resultatFitxatge = $this->fitxatge->fitxar($professor);
            $missatge = $obertura['porta_oberta']
                ? "Porta oberta ({$direccio->value})"
                : 'No s\'ha pogut obrir la porta';

            $log->info('Resultat d’accés al pàrquing', [
                'sentit' => $direccio->value,
                'resultat' => $resolucio['resultat'],
                'professor' => $professor,
                'porta_oberta' => $obertura['porta_oberta'],
                'intents_domotica' => $obertura['intents'],
                'fitxatge' => (bool) $resultatFitxatge,
            ]);

            return response()->json(['status' => $missatge]);
        } finally {
            $this->access->finalitzarReservaAcces($professor, $direccio->value, 30);
        }
    }


    /**
     * Accepta payloads heterogenis (Milesight, etc.).
     *
     * @return array{0: string, 1: mixed}
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

        $matricula = Cotxe::normalizeMatricula($rawPlate);
        $device    = $rawDevice ?: ($direccio === Direccio::Entrada ? 'Cam_exterior' : 'Cam_interior');

        return [$matricula, $device];
    }
}
