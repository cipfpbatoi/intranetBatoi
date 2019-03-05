<?php

namespace Intranet\Http\Controllers;

use Illuminate\Http\Request;
use Intranet\Http\Controllers\BaseController;
use Intranet\Entities\Horario;
use Intranet\Entities\Profesor;
use Styde\Html\Facades\Alert;
use Illuminate\Support\Facades\Storage;
use Intranet\Botones\BotonImg;
use Illuminate\Support\Facades\Session;

class HorarioController extends IntranetController
{

    protected $model = 'Horario';
    protected $perfil = 'profesor';
    protected $gridFields = ['XModulo','XOcupacion' ,'dia_semana', 'desde', 'aula'];
    protected $modal = true;
   

    public function changeTable($dni,$redirect=true){
        $correcto = false;
        if (Storage::disk('local')->exists('/horarios/'.$dni.'.json'))
            if ($fichero = Storage::disk('local')->get('/horarios/'.$dni.'.json')) {
                $data=json_decode($fichero);
                switch ($data->estado) {
                    case "Aceptado":
			// Guardfa el fichero original
                        Storage::disk('local')->put('/horarios/horariosCambiados/'.$dni.'.json', json_encode($data));
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
                        if (Storage::disk('local')->put('/horarios/'.$dni.'.json', json_encode($data)))
                                $correcto = true;
                        else Alert::warning("Horari amb dni $dni modificat però no s\'ha pogut guardar el fitxer");
                        break;
                    case "Guardado":
                        Alert::info("Horari amb dni $dni ja està guardat");
                        break;
                    default:
                        Alert::warning("Horari amb dni $dni no està aceptat");
                }
              } else  Alert::danger("Horari amb dni $dni no té canvis");
        if ($redirect) return back();
        else return $correcto;
    }

    public function changeTableAll(){
        $correctos = 0;
        // Cogemos todos los profesores y los vamos recorriendo
        $profes = Profesor::select('dni')->Activo()->get();
        foreach ($profes as $profe) {
            $correctos += $this->changeTable($profe->dni,false);
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
    
    protected function iniBotones()
    {
        $this->panel->setBotonera([],['edit']);
    }
    
    public function index(){
        return $this->modificarHorario(Session::get('horarioProfesor'));
    }
    
    protected function modificarHorario($idProfesor){
        Session::forget('redirect'); //buida variable de sessió redirect ja que sols se utiliza en cas de direccio
        Session::put('horarioProfesor',$idProfesor);
        $this->titulo = ['quien' => Profesor::find($idProfesor)->fullName]; // paràmetres per al titol de la vista
        $this->iniBotones();
        return $this->grid(Horario::Profesor($idProfesor)->get());
    }
}
