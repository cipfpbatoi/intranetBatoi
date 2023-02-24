<?php

namespace Intranet\Http\Controllers\API;

use Intranet\Entities\Empresa;
use Intranet\Http\Resources\EmpresaResource;

class EmpresaController extends ApiBaseController
{
    protected $model = 'Empresa';
    
    public function indexConvenio()
    {
        $data = EmpresaResource::collection(Empresa::where('concierto', '>', 0)->where('europa', 0)->get());
        return $this->sendResponse($data, 'OK');
    }
}
