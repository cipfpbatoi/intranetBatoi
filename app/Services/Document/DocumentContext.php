<?php

namespace Intranet\Services\Document;

use Intranet\Entities\Documento;

/**
 * Servei DocumentContext.
 */
class DocumentContext
{
    private ?Documento $document;
    private ?string $link;
    private bool $isFile;

    public function __construct(?Documento $document, ?string $link, bool $isFile)
    {
        $this->document = $document;
        $this->link = $link;
        $this->isFile = $isFile;
    }

    public function document(): ?Documento
    {
        return $this->document;
    }

    public function link(): ?string
    {
        return $this->link;
    }

    public function isFile(): bool
    {
        return $this->isFile;
    }
}
