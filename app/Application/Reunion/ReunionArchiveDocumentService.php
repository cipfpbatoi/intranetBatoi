<?php

declare(strict_types=1);

namespace Intranet\Application\Reunion;

use Illuminate\Support\Carbon;
use Intranet\Entities\Reunion;
use Intranet\Services\Document\TipoReunionService;
use Intranet\Services\General\GestorService;

/**
 * Registra una acta arxivada en el gestor documental.
 */
class ReunionArchiveDocumentService
{
    /**
     * Guarda o actualitza el registre documental vinculat a la reunió.
     */
    public function save(Reunion $reunion, string $course): void
    {
        $creatorName = (string) $reunion->Creador->FullName;

        (new GestorService($reunion))->save([
            'curso' => $course,
            'propietario' => $creatorName,
            'tipoDocumento' => 'Acta',
            'descripcion' => $reunion->descripcion,
            'fichero' => $reunion->fichero,
            'supervisor' => $creatorName,
            'grupo' => str_replace(' ', '_', $reunion->Xgrupo),
            'tags' => TipoReunionService::find($reunion->tipo)->vliteral,
            'created_at' => new Carbon($reunion->fecha),
            'rol' => config('roles.rol.profesor'),
        ]);
    }
}
