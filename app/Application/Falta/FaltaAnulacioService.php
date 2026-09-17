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

/** Anul·la faltes autoritzades encara no incloses en un tancament mensual. */
class FaltaAnulacioService
{
    /** Allibera el dia d'assumptes particulars i retira el justificant, si correspon. */
    public function annullar(int $id, string $motiu, string $dniDireccio): void
    {
        $motiu = trim($motiu);
        if ($motiu === '') {
            throw ValidationException::withMessages(['motiuAnulacio' => 'Cal indicar el motiu de l’anul·lació.']);
        }

        $ruta = DB::transaction(function () use ($id, $motiu, $dniDireccio): ?string {
            $falta = Falta::query()->lockForUpdate()->findOrFail($id);
            if ((int) $falta->estado !== 3 || $falta->idDocumento !== null) {
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
            Log::info('Falta autoritzada anul·lada per Direcció', [
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
