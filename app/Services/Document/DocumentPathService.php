<?php

namespace Intranet\Services\Document;

use Illuminate\Support\Facades\File;

/**
 * Servei DocumentPathService.
 */
class DocumentPathService
{
    public function resolvePath(DocumentContext $context): ?string
    {
        if (!$context->isFile()) {
            return null;
        }

        return $context->link();
    }

    public function exists(DocumentContext $context): bool
    {
        $path = $this->resolvePath($context);
        return $path ? $this->existsPath($path) : false;
    }

    public function mimeType(DocumentContext $context): ?string
    {
        $path = $this->resolvePath($context);
        if (!$path || !$this->existsPath($path)) {
            return null;
        }

        return File::mimeType($path) ?: null;
    }

    public function responseFile(DocumentContext $context)
    {
        $path = $this->resolvePath($context);
        if (!$path || !$this->existsPath($path)) {
            return null;
        }

        $mime = $this->mimeType($context);
        $headers = $mime ? ['Content-Type' => $mime] : [];

        return response()->file($path, $headers);
    }

    public function existsPath(string $path): bool
    {
        return file_exists($path);
    }

    public function responseFromPath(string $path)
    {
        if (!$this->existsPath($path)) {
            return null;
        }

        $mime = File::mimeType($path) ?: null;
        $headers = $mime ? ['Content-Type' => $mime] : [];

        return response()->file($path, $headers);
    }
}
