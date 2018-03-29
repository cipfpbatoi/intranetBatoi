<?php

namespace Intranet\Http\Controllers\API;

use Intranet\Entities\Reserva;
use Illuminate\Http\Request;

class ReservaController extends ApiBaseController
{

    protected $model = 'Reserva';
    
    public function show($cadena,$send=true){
        $data = parent::show($cadena,false);
        foreach ($data as $uno){
            if (isset($uno->Profesor->nombre))
                $uno->nomProfe = $uno->Profesor->ShortName;
        }
        return $this->sendResponse($data, 'OK');
    }
}
