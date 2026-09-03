<?php

namespace Intranet\Application\Reunion;

use Intranet\Entities\Documento;
use Intranet\Services\Document\TipoReunionService;
use RuntimeException;
use ZipArchive;

/**
 * Prepara l'exportació en ZIP de les actes d'avaluació arxivades.
 */
class ReunionActaExportService
{
    private const ACTA_REUNION_TYPE = 7;
    private const DOCUMENT_TYPE = 'Acta';

    /**
     * Genera un ZIP amb les actes d'avaluació del curs indicat.
     *
     * @param int $numero
     * @param string|null $curso
     * @return array{path: string|null, filename: string, documents: int, exported: int, missing: array<int, string>}
     */
    public function export(int $numero, ?string $curso = null): array
    {
        if (!$this->isValidEvaluation($numero)) {
            return $this->emptyResult($numero, $curso);
        }

        $curso = $curso ?? curso();
        $documents = $this->documents($numero, $curso);
        $filename = $this->zipFilename($numero, $curso);
        $zipPath = storage_path('app/zip/' . $filename);
        $missing = [];
        $exported = 0;

        if ($documents->isEmpty()) {
            return $this->result(null, $filename, 0, 0, []);
        }

        $this->ensureZipDirectory();

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException("No s'ha pogut crear el ZIP d'actes d'avaluació.");
        }

        foreach ($documents as $document) {
            $path = storage_path('app/' . $document->fichero);
            if (!$document->fichero || !file_exists($path)) {
                $missing[] = $this->missingLabel($document);
                continue;
            }

            $zip->addFile($path, $this->entryName($document));
            $exported++;
        }

        if ($missing !== []) {
            $zip->addFromString('_fitxers_omesos.txt', implode(PHP_EOL, $missing) . PHP_EOL);
        }

        $zip->close();

        if ($exported === 0) {
            @unlink($zipPath);
            return $this->result(null, $filename, $documents->count(), 0, $missing);
        }

        return $this->result($zipPath, $filename, $documents->count(), $exported, $missing);
    }

    /**
     * Indica si el número correspon a una avaluació definida.
     */
    public function isValidEvaluation(int $numero): bool
    {
        return array_key_exists($numero, TipoReunionService::find(self::ACTA_REUNION_TYPE)->numeracion);
    }

    /**
     * Consulta els documents d'acta vinculats a reunions d'avaluació arxivades.
     *
     * @param int $numero
     * @param string $curso
     * @return \Illuminate\Support\Collection<int, Documento>
     */
    private function documents(int $numero, string $curso)
    {
        return Documento::query()
            ->select('documentos.*')
            ->join('reuniones', 'documentos.idDocumento', '=', 'reuniones.id')
            ->where('documentos.tipoDocumento', self::DOCUMENT_TYPE)
            ->where('documentos.curso', $curso)
            ->where('reuniones.tipo', self::ACTA_REUNION_TYPE)
            ->where('reuniones.numero', $numero)
            ->where('reuniones.archivada', 1)
            ->orderBy('documentos.grupo')
            ->orderBy('documentos.idDocumento')
            ->get();
    }

    /**
     * Crea el directori temporal de ZIP si no existeix.
     */
    private function ensureZipDirectory(): void
    {
        $directory = storage_path('app/zip');
        if (!is_dir($directory)) {
            mkdir($directory, 0775, true);
        }
    }

    /**
     * Nom públic del ZIP generat.
     */
    private function zipFilename(int $numero, string $curso): string
    {
        $label = TipoReunionService::find(self::ACTA_REUNION_TYPE)->numeracion[$numero] ?? $numero;

        return sprintf('actes_avaluacio_%s_%s.zip', $curso, $this->slug((string) $label));
    }

    /**
     * Nom intern del fitxer dins del ZIP.
     */
    private function entryName(Documento $document): string
    {
        $extension = pathinfo((string) $document->fichero, PATHINFO_EXTENSION) ?: 'pdf';
        $grupo = $this->slug((string) ($document->grupo ?: 'sense_grup'));

        return sprintf('%s/Acta_%s.%s', $grupo, $document->idDocumento, $extension);
    }

    /**
     * Etiqueta d'un document omés perquè no té fitxer llegible.
     */
    private function missingLabel(Documento $document): string
    {
        return sprintf(
            'Document #%s, acta #%s, grup %s, fitxer %s',
            $document->id,
            $document->idDocumento,
            $document->grupo ?: 'sense grup',
            $document->fichero ?: 'sense fitxer'
        );
    }

    /**
     * Normalitza fragments de nom de fitxer.
     */
    private function slug(string $value): string
    {
        $value = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value) ?: $value;
        $value = preg_replace('/[^A-Za-z0-9]+/', '_', $value) ?: '';

        return trim($value, '_') ?: 'document';
    }

    /**
     * Resultat buit per a avaluacions no vàlides.
     *
     * @return array{path: string|null, filename: string, documents: int, exported: int, missing: array<int, string>}
     */
    private function emptyResult(int $numero, ?string $curso): array
    {
        return $this->result(null, $this->zipFilename($numero, $curso ?? curso()), 0, 0, []);
    }

    /**
     * Construeix el resultat de l'exportació.
     *
     * @param string|null $path
     * @param string $filename
     * @param int $documents
     * @param int $exported
     * @param array<int, string> $missing
     * @return array{path: string|null, filename: string, documents: int, exported: int, missing: array<int, string>}
     */
    private function result(?string $path, string $filename, int $documents, int $exported, array $missing): array
    {
        return compact('path', 'filename', 'documents', 'exported', 'missing');
    }
}
