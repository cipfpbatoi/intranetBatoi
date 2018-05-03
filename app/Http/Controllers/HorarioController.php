<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Intranet\Http\Controllers\BaseController;
use Intranet\Entities\Horario;
use Intranet\Entities\Profesor;
use Styde\Html\Facades\Alert;
use Illuminate\Support\Facades\Storage;

class HorarioController extends BaseController
{

    protected $model = 'Horario';

    public function changeTable($dni){
        if (Storage::disk('local')->exists('/horarios/'.$dni.'.json'))
            if ($fichero = Storage::disk('local')->get('/horarios/'.$dni.'.json')) {
                $data=json_decode($fichero);
                switch ($data->estado) {
                    case "Aceptado":
                        // Modifica la tabla
                        foreach ($data->cambios as $cambio) {
                        
                           $de=explode("-",$cambio->de);
                           $a=explode("-", $cambio->a);

                           $horario = Horario::dia($de[1])->orden($de[0])->Profesor($dni)->first();
                           $horario->dia_semana = $a[1];
                           $horario->sesion_orden = $a[0];
                           $horario->save();
                        }

                        // Pon el estado del fichero como "Guardado"
                        $data->estado="Guardado";
                        $data->cambios=[];
                        if (Storage::disk('local')->put('/horarios/'.$dni.'.json', json_encode($data))) return 1;
                        else Alert::warning("Horari amb dni $dni modificat però no s\'ha pogut guardar el fitxer");
                        break;
                    case "Guardado":
                        Alert::info("Horari amb dni $dni ja està guardat");
                        break;
                    default:
                        Alert::warning("Horari amb dni $dni no està aceptat");
                }
              } else  Alert::danger("Horari amb dni $dni no té canvis");
        return 0;
    }

    public function changeTableAll(){
        $correctos = 0;
        // Cogemos todos los profesores y los vamos recorriendo
        $profes = Profesor::select('dni')->Activo()->get();
        foreach ($profes as $profe) {
            $correctos += $this->changeTable($profe->dni);
        }
        Alert::success("He fet $correctos canvis d'horaris");
        return back();
    }

    public function changeIndex() {
        return view('horario.change');
    }
    
    public function horarioCambiar(){
        return redirect("/profesor/".AuthUser()->dni."/horario-cambiar");
    }

}
