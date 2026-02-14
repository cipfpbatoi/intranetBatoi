<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Intranet\Services\Mail\FctMailService;

class FctMailController extends Controller
{
    protected $fctMailService;

    public function __construct(FctMailService $fctMailService)
    {
        parent::__construct();
        $this->fctMailService = $fctMailService;
    }

    public function showMailById($id, $documento)
    {
        return $this->fctMailService->getMailById($id, $documento);
    }

    public function showMailByRequest(Request $request, $documento)
    {
        return $this->fctMailService->getMailByRequest($request, $documento);
    }
}

