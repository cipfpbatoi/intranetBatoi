<?php

declare(strict_types=1);

namespace Intranet\Application\Falta;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Intranet\Application\AssumpteParticular\AssumpteParticularArchiveService;
use Intranet\Entities\AssumpteParticular;
use Intranet\Entities\Documento;
use Intranet\Entities\Falta;

/** Retira faltes abans d'imprimir l'informe mensual i allibera les concessions associades. */
class FaltaAnulacioService
{
    /** Allibera el dia d'assumptes particulars i retira el justificant, si correspon. */
    public function annullar(int $id, string $motiu, string $dniDireccio): void
    {
        $this->retirar($id, $motiu, $dniDireccio, true);
    }

    /** Esborra una falta de qualsevol estat mentre no s'haja imprés l'informe mensual. */
    public function esborrar(int $id, string $motiu, string $dniDireccio): void
    {
        $this->retirar($id, $motiu, $dniDireccio, false);
    }

    /** Revalida el tancament amb bloqueig i compartix la neteja i el registre de l'operació. */
    private function retirar(int $id, string $motiu, string $dniDireccio, bool $nomesAutoritzada): void
    {
        $motiu = trim($motiu);
        if ($motiu === '') {
            throw ValidationException::withMessages(['motiuAnulacio' => 'Cal indicar el motiu de l’anul·lació.']);
        }

        $ruta = DB::transaction(function () use ($id, $motiu, $dniDireccio, $nomesAutoritzada): ?string {
            $falta = Falta::query()->lockForUpdate()->findOrFail($id);
            if ($falta->idDocumento !== null || (int) $falta->estado === 4) {
                throw ValidationException::withMessages([
                    'motiuAnulacio' => __('falta.esborrat.informe_impres'),
                ]);
            }
            if ($nomesAutoritzada && (int) $falta->estado !== 3) {
                throw ValidationException::withMessages([
                    'motiuAnulacio' => 'Només es pot anul·lar una falta autoritzada que no estiga tancada mensualment.',
                ]);
            }

            $peticio = AssumpteParticular::query()->where('falta_id', $id)->lockForUpdate()->first();
            $ruta = (string) ($peticio?->resolucio_document ?: $falta->fichero);
            if ($peticio !== null) {
                if ($peticio->estat !== AssumpteParticular::ESTAT_AUTORITZADA) {
                    throw ValidationException::withMessages(['motiuAnulacio' => 'La petició ja no està autoritzada.']);
                }
                $peticio->forceFill([
                    'estat' => AssumpteParticular::ESTAT_CANCEL_LADA,
                    'cancel_lada_at' => now(),
                    'resolucio' => sprintf('Anul·lada per Direcció (%s): %s', $dniDireccio, $motiu),
                    'falta_id' => null,
                    'resolucio_document' => null,
                ])->save();
                Documento::query()
                    ->where('tipoDocumento', AssumpteParticularArchiveService::TIPO_DOCUMENTO)
                    ->where('fichero', $ruta)
                    ->delete();
            }

            $falta->delete();
            Log::info($nomesAutoritzada ? 'Falta autoritzada anul·lada per Direcció' : 'Falta esborrada per Direcció', [
                'falta_id' => $id,
                'direccio_dni' => $dniDireccio,
                'motiu' => $motiu,
            ]);

            return $ruta !== '' ? $ruta : null;
        });

        if ($ruta !== null && Storage::disk('local')->exists($ruta)) {
            Storage::disk('local')->delete($ruta);
        }
    }
}
