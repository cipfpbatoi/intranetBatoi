<?php

namespace Intranet\Services\Document;

use Intranet\Http\PrintResources\PrintResource;
use Exception;
use Intranet\Exceptions\IntranetException;
use Illuminate\Support\Facades\Log;

/**
 * Servei per preparar PDFs de plantilles FDF i concatenar fitxers resultants.
 */
class FDFPrepareService
{
    /**
     * Genera un PDF a partir d'un recurs imprimible i retorna la ruta absoluta.
     *
     * @param PrintResource $resource
     * @param mixed $id
     * @return string|null
     */
    public static function exec(PrintResource $resource, $id=null)
    {
        $id = $id??str_shuffle('abcdeft12');
        $nameFile = storage_path("tmp/{$id}_{$resource->getFile()}");
        $tmpDir = dirname($nameFile);

        if (!is_dir($tmpDir)) {
            mkdir($tmpDir, 0775, true);
        }

        if (file_exists($nameFile)) {
            unlink($nameFile);
        }
        try {
            app(PdfFormService::class)->fillForResource(
                'fdf/' . $resource->getFile(),
                $resource->toArray(),
                $nameFile,
                (bool) $resource->getFlatten(),
                $resource->getStamp() ? public_path('fdf/' . $resource->getStamp()) : null
            );

            if (!file_exists($nameFile)) {
                Log::error('No s\'ha generat el fitxer PDF', [
                    'file' => $nameFile,
                    'resource' => $resource->getFile(),
                ]);
                return null;
            }

            return $nameFile;
        }  catch (Exception $e) {
            report($e);
            Log::error('Excepció generant PDF', [
                'message' => $e->getMessage(),
                'file' => $nameFile,
                'resource' => $resource->getFile(),
            ]);
            return null;
        }
    }

    /**
     * Concatena diversos PDFs i retorna la ruta relativa del resultat.
     *
     * @param array<int, string> $pdfs
     * @param string $nameFile
     * @return string
     */
    public static function joinPDFs($pdfs, $nameFile)
    {
        $tmpFileName = storage_path("tmp/$nameFile.pdf");
        try {
            app(PdfMergeService::class)->merge($pdfs, $tmpFileName);
        } catch (Exception $e) {
            report($e);
            Log::error('Error concatenant PDFs amb FPDI', [
                'file' => $tmpFileName,
                'message' => $e->getMessage(),
            ]);
            throw new IntranetException(
                "No s'ha pogut concatenar els PDFs.",
                500,
                "No s'ha pogut generar el PDF adjunt.",
                true,
                [
                    'nameFile' => $nameFile,
                    'pdfCount' => is_array($pdfs) ? count($pdfs) : null,
                ],
                $e
            );
        }
        return "tmp/$nameFile.pdf";
    }
}
