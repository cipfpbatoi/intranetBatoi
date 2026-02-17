<?php

namespace Intranet\Http\Controllers\API;

use Intranet\Entities\Empresa;
use Intranet\Http\Resources\EmpresaResource;

class EmpresaController extends ApiBaseController
{
    protected $model = 'Empresa';
    
    public function indexConvenio()
    {
        $data = EmpresaResource::collection(
            Empresa::query()
                ->select([
                    'id',
                    'concierto',
                    'nombre',
                    'direccion',
                    'localidad',
                    'telefono',
                    'email',
                    'cif',
                    'actividad',
                    'fichero',
                ])
                ->where('concierto', '>', 0)
                ->where('europa', 0)
                ->orderBy('nombre')
                ->get()
        );
        return $this->sendResponse($data, 'OK');
    }
}
