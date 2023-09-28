<?php

namespace Intranet\Http\Controllers\API;

use Intranet\Entities\Comision;
use Illuminate\Http\Request;
use \DB;

class ComisionController extends ApiBaseController
{

    protected $model = 'Comision';
    protected $rules = [
        'kilometraje' => 'Integer',
        'profesor' => 'required',
        'servicio' => 'required',
        'entrada' => 'after:salida',
        'matricula' => 'required_with:marca'
    ];

    public function autorizar()
    {
        $data = Comision::join('profesores', 'idProfesor', '=', 'dni')
                ->select('comisiones.*', DB::raw('CONCAT(apellido1," ",apellido2,",",nombre) AS nombre'))
                ->whereIn('estado', [1, 2])
                ->whereNull('deleted_at')
                ->get();
        return $this->sendResponse($data, 'OK');
    }

    public function prePay($dni)
    {
        $data = Comision::where('idProfesor', $dni)
                ->where('estado', 4)
                ->get();
        foreach ($data as $item) {
            $item->estado = 6;
            $item->save();
        }
        return $this->sendResponse($data, 'OK');
    }

}
