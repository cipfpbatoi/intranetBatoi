<?php

namespace Intranet\Services\Document;

/**
 * Servei DocumentAccessChecker.
 */
class DocumentAccessChecker
{
    public function isAllowed(DocumentContext $context): bool
    {
        $document = $context->document();

        return !($document && !in_array($document->rol, rolesUser(authUser()->rol)));
    }
}
