<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Intranet\Http\Controllers\BaseController;
use Intranet\Entities\Horario;
use Intranet\Entities\Profesor;
use Illuminate\Support\Facades\Storage;

class HorarioController extends BaseController
{

    protected $model = 'Horario';

    public function changeTable($dni){
        if (Storage::disk('local')->exists('/horarios/'.$dni.'.json'))
            if ($data = Storage::disk('local')->get('/horarios/'.$dni.'.json'))
                switch ($data->estado) {
                    case "Aceptado":
                        // Modifica la tabla
                        foreach ($data->cambios as $cambio) {
                           $de=explode("-",$cambio['de']);
                           $a=explode("-", $cambio['a']);

                           $horario = Horario::dia($de[1])->orden($de[0])->Profesor($dni)->first();
                           $horario->dia_semana = $a[1];
                           $horario->sesion_orden = $a[0];
                           $horario->save();
                        }

                        // Pon el estado del fichero como "Guardado"
                        $data->estado="Guardado";
                        $data->cambios="[]";
                        if (Storage::disk('local')->put('/horarios/'.$dni.'.json', $data))
                            return $this->sendResponse('Guardado Correctament','OK');
                        else
                            return $this->sendError('Horari modificat però no s\'ha pogut guardar el fitxer');
                        break;
                    case "Guardado":
                        return $this->sendError('L\'horari ja està guardat');
                        break;
                    default:
                        return $this->sendError('No està aceptat');
            else 
                return $this->sendError('No hi han canvis');
        else
           return $this->sendError('No s\'ha fet proposta de canvis'); 
    }

    public function changeTableAll(){
        $data=[];
        // Cogemos todos los profesores y los vamos recorriendo
        $profes = Profesor::select('dni')->Activo()->get();
        foreach ($profes as $profe) {
            $data[$profe->dni]=changeTable($profe->dni);
        }
        return $this->sendResponse($data, 'OK');
    }

    public function changeIndex() {
        return view('horario.change');
    }

}
