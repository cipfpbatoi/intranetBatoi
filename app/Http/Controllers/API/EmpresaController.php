<?php

namespace Intranet\Http\Controllers\API;

use Intranet\Application\Empresa\EmpresaService;
use Intranet\Http\Resources\EmpresaResource;

class EmpresaController extends ApiBaseController
{
    private ?EmpresaService $empresaService = null;

    protected $model = 'Empresa';

    public function __construct(?EmpresaService $empresaService = null)
    {
        parent::__construct();
        $this->empresaService = $empresaService;
    }

    private function empreses(): EmpresaService
    {
        if ($this->empresaService === null) {
            $this->empresaService = app(EmpresaService::class);
        }

        return $this->empresaService;
    }
    
    public function indexConvenio()
    {
        $data = EmpresaResource::collection($this->empreses()->convenioList());
        return $this->sendResponse($data, 'OK');
    }
}
