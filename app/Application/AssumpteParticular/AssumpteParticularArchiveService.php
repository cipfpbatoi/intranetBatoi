<?php

declare(strict_types=1);

namespace Intranet\Application\AssumpteParticular;

use Illuminate\Support\Facades\Storage;
use Intranet\Entities\AssumpteParticular;
use Intranet\Entities\Documento;
use Intranet\Entities\Profesor;

/** Conserva les resolucions firmades en el gestor documental entre cursos. */
class AssumpteParticularArchiveService
{
    public const TIPO_DOCUMENTO = 'AssumpteParticular';

    /** Arxiva una resolució sense duplicar-la si ja existeix. */
    public function arxivar(AssumpteParticular $peticio, Profesor $professor): Documento
    {
        $ruta = (string) $peticio->resolucio_document;
        if ($ruta === '' || !Storage::disk('local')->exists($ruta)) {
            throw new AssumpteParticularException('Falta el PDF firmat; no es pot arxivar la resolució.');
        }

        $document = Documento::query()->firstOrNew(['fichero' => $ruta]);
        if ($document->exists && $document->tipoDocumento !== self::TIPO_DOCUMENTO) {
            throw new AssumpteParticularException('La ruta del PDF ja pertany a un altre document.');
        }
        $document->fill([
            'tipoDocumento' => self::TIPO_DOCUMENTO,
            'curso' => $peticio->curs,
            'propietario' => $professor->FullName,
            'propietario_dni' => $peticio->idProfesor,
            'descripcion' => 'Resolució d’assumpte particular del ' . $peticio->data_gaudi->format('d/m/Y'),
            'fichero' => $ruta,
            'rol' => 1,
        ]);
        $document->save();

        return $document;
    }

    /** Assegura l'arxiu abans de buidar les taules temporals del curs. */
    public function arxivarPendents(): void
    {
        AssumpteParticular::query()
            ->with('profesor')
            ->where('estat', AssumpteParticular::ESTAT_AUTORITZADA)
            ->where('origen', AssumpteParticular::ORIGEN_SOLLICITUD)
            ->orderBy('id')
            ->chunk(100, function ($peticions): void {
                foreach ($peticions as $peticio) {
                    if (!$peticio->profesor) {
                        throw new AssumpteParticularException('No es pot arxivar una petició sense professor.');
                    }
                    $this->arxivar($peticio, $peticio->profesor);
                }
            });
    }
}
