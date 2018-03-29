<?php

namespace Intranet\Http\Controllers\API;

use Intranet\Entities\Empresa;
use Illuminate\Http\Request;

class EmpresaController extends ApiBaseController
{
    protected $model = 'Empresa';
    
    public function indexConvenio(){
        $data = Empresa::where('concierto','>',0)->get();
        return $this->sendResponse($data, 'OK');
    }
   
}
