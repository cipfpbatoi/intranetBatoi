<?php

namespace Intranet\Http\Controllers\API;

use Illuminate\Http\Request;
use Intranet\Http\Requests;
use Intranet\Http\Controllers\Controller;
use Intranet\Http\Controllers\API\ApiBaseController;
use Intranet\Entities\Guardia;

class GuardiaController extends ApiBaseController
{

    protected $model = 'Guardia';
    
    public function show($cadena,$send=true){
        $data = parent::show($cadena,false);
        return $this->sendResponse($data, 'OK');
    }

    public function getServerTime()
    {
        return response()->json([
            'date' => now()->toDateString(),
            'time' => now()->toTimeString(),
        ]);
    }



    
}
