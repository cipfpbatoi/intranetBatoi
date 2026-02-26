<?php

namespace Intranet\Services\Document;

use Intranet\Services\UI\AppAlert as Alert;

class DocumentResponder
{
    private DocumentAccessChecker $accessChecker;
    private DocumentPathService $pathService;

    public function __construct(?DocumentAccessChecker $accessChecker = null, ?DocumentPathService $pathService = null)
    {
        $this->accessChecker = $accessChecker ?? new DocumentAccessChecker();
        $this->pathService = $pathService ?? new DocumentPathService();
    }

    public function respond(DocumentContext $context)
    {
        if (!$this->accessChecker->isAllowed($context)) {
            Alert::warning('Sense Permisos');
            return back();
        }

        if ($context->isFile() && $this->pathService->exists($context)) {
            return $this->pathService->responseFile($context);
        }

        if ($context->isFile() === false && $context->link()) {
            return redirect($context->link());
        }

        Alert::warning(trans("messages.generic.nodocument"));
        return back();
    }
}
