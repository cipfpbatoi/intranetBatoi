<?php

namespace Intranet\Services\Document;

use Styde\Html\Facades\Alert;

class DocumentResponder
{
    private DocumentAccessChecker $accessChecker;

    public function __construct(?DocumentAccessChecker $accessChecker = null)
    {
        $this->accessChecker = $accessChecker ?? new DocumentAccessChecker();
    }

    public function respond(DocumentContext $context)
    {
        if (!$this->accessChecker->isAllowed($context)) {
            Alert::warning('Sense Permisos');
            return back();
        }

        if ($context->isFile() && $context->link() && file_exists($context->link())) {
            return response()->file($context->link());
        }

        if ($context->isFile() === false && $context->link()) {
            return redirect($context->link());
        }

        Alert::warning(trans("messages.generic.nodocument"));
        return back();
    }
}
