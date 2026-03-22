<?php

declare(strict_types=1);

namespace Intranet\Application\Documento;

use Illuminate\Support\Collection;
use Intranet\Entities\Adjunto;
use Intranet\Entities\Documento;
use Intranet\Entities\Grupo;
use Intranet\Entities\Profesor;
use Intranet\Services\Document\CreateOrUpdateDocumentAction;
use Intranet\Services\UI\AppAlert as Alert;
use RuntimeException;
use ZipArchive;

/**
 * Orquestra la consolidació de documentació de qualitat FCT en un ZIP documental.
 */
class FctQualitatUploadService
{
    /**
     * Genera un document ZIP de qualitat FCT per al tutor indicat.
     *
     * @param Profesor $profesor
     * @param Grupo $grupo
     * @param Collection<int, Adjunto> $documents
     * @return Documento|null
     */
    public function createZipDocument(Profesor $profesor, Grupo $grupo, Collection $documents): ?Documento
    {
        $elemento = (new CreateOrUpdateDocumentAction())->fromArray([
            'curso' => Curso(),
            'propietario' => $profesor->FullName,
            'supervisor' => $profesor->FullName,
            'activo' => true,
            'tipoDocumento' => 'FCT',
            'idDocumento' => null,
            'ciclo' => $grupo->Ciclo->ciclo,
            'grupo' => $grupo->nombre,
            'tags' => 'Fct,Entrevista,Alumnat,Instructor',
            'descripcion' => 'Documentació FCT Cicle ' . $grupo->Ciclo->ciclo,
        ]);

        $path = 'gestor/' . curso() . '/FCT/';
        $storagePath = storage_path('app/' . $path);
        $zipFileName = $elemento->id . '_FCT.zip';
        $zipAbsolutePath = $storagePath . $zipFileName;
        $elemento->fichero = $path . $zipFileName;

        $this->ensureDirectoryExists($storagePath);

        $zip = new ZipArchive();
        $opened = $zip->open($zipAbsolutePath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        if ($opened !== true) {
            $elemento->delete();
            throw new RuntimeException('No s\'ha pogut obrir el ZIP de qualitat FCT.');
        }

        $problem = false;
        $esborrar = [];

        foreach ($documents as $document) {
            $file = $this->resolveAdjuntoPath($document);
            if (file_exists($file)) {
                $zip->addFile($file, $document->name);
                $esborrar[$document->id] = $file;
            } else {
                $problem = true;
                Alert::danger("Problemes per a guardar el fitxer: $file");
            }
        }

        $zip->close();

        if ($problem) {
            $elemento->delete();
            @unlink($zipAbsolutePath);
            return null;
        }

        $elemento->save();
        foreach ($esborrar as $adjuntoId => $file) {
            Adjunto::destroy($adjuntoId);
            @unlink($file);
        }

        return $elemento;
    }

    private function ensureDirectoryExists(string $storagePath): void
    {
        if (!file_exists($storagePath) && !mkdir($storagePath, 0777, true) && !is_dir($storagePath)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $storagePath));
        }
    }

    private function resolveAdjuntoPath(Adjunto $document): string
    {
        $storageFile = storage_path("app/public/adjuntos/{$document->route}/{$document->title}.{$document->extension}");
        if (file_exists($storageFile)) {
            return $storageFile;
        }

        return public_path("storage/adjuntos/{$document->route}/{$document->title}.{$document->extension}");
    }
}
