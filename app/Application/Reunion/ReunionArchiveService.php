<?php

declare(strict_types=1);

namespace Intranet\Application\Reunion;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\DB;
use Intranet\Entities\Reunion;
use Throwable;

/**
 * Orquestra l'arxivament transaccional d'una acta de reunió.
 */
class ReunionArchiveService
{
    /**
     * @param ReunionContinuityService $continuityService
     * @param ReunionArchivePdfService $pdfService
     * @param ReunionArchiveDocumentService $documentService
     * @param Filesystem $filesystem
     */
    public function __construct(
        private ReunionContinuityService $continuityService,
        private ReunionArchivePdfService $pdfService,
        private ReunionArchiveDocumentService $documentService,
        private Filesystem $filesystem
    ) {
    }

    /**
     * Arxiva l'acta i desfà tots els canvis si falla qualsevol pas.
     *
     * @throws Throwable
     */
    public function archive(Reunion $reunion, string $course): ReunionArchiveResult
    {
        if ($reunion->archivada) {
            return ReunionArchiveResult::alreadyArchived();
        }

        $createdFile = null;

        try {
            DB::transaction(function () use ($reunion, $course, &$createdFile): void {
                $relativePath = (string) $reunion->fichero;

                if ($relativePath === '') {
                    $this->continuityService->normaliseEmptySummaries($reunion);
                    $relativePath = sprintf('gestor/%s/Reunion/Acta_%s.pdf', $course, $reunion->id);
                    $absolutePath = storage_path('app/' . $relativePath);

                    if (!$this->filesystem->exists($absolutePath)) {
                        $createdFile = $absolutePath;
                        $this->pdfService->save($reunion, $absolutePath);
                    }
                }

                $reunion->archivada = true;
                $reunion->fichero = $relativePath;
                $this->documentService->save($reunion, $course);
                $reunion->save();
            });
        } catch (Throwable $exception) {
            if ($createdFile !== null && $this->filesystem->isFile($createdFile)) {
                $this->filesystem->delete($createdFile);
            }

            throw $exception;
        }

        return ReunionArchiveResult::archived();
    }
}
