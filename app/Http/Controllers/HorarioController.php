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


    private function getJsonFromFile($dni){
        if (Storage::disk('local')->exists('/horarios/'.$dni.'.json') && $fichero = Storage::disk('local')->get('/horarios/'.$dni.'.json')) {
            return json_decode($fichero);
        }
        return null;
    }

    private function changeHorary($dni,$cambios){
        foreach ($cambios as $cambio) {

            $de=explode("-",$cambio->de);
            $a=explode("-", $cambio->a);

            $horario = Horario::dia($de[1])->orden($de[0])->Profesor($dni)->first();
            if ($horario){
                $horario->dia_semana = $a[1];
                $horario->sesion_orden = $a[0];
                $horario->save();
            } else {
                Alert::info("Horari".$de[1].' '.$de[0]." del profesor $dni no trobat");
            }

        }
    }
    private function saveCopy($dni,$data){
        if (! Storage::disk('local')->exists('/horarios/horariosCambiados/'.$dni.'.json')) {
            Storage::disk('local')->put('/horarios/horariosCambiados/' . $dni . '.json', json_encode($data));
        }

    }

    public function changeTable($dni,$redirect=true){
        $correcto = false;
        if ($data = $this->getJsonFromFile($dni)){
                switch ($data->estado) {
                    case "Aceptado":
			            $this->saveCopy($dni,$data);
                        $this->changeHorary($dni,$data->cambios);

                        $data->estado="Guardado";
                        $data->cambios=[];
                        if (Storage::disk('local')->put('/horarios/'.$dni.'.json', json_encode($data))) {
                            $correcto = true;
                        }
                        else {
                            Alert::warning("Horari amb dni $dni modificat però no s\'ha pogut guardar el fitxer");
                        }
                        break;
                    case "Guardado":
                        Alert::info("Horari amb dni $dni ja està guardat");
                        break;
                    default:
                        Alert::warning("Horari amb dni $dni no està aceptat");
                }
        } else {
            Alert::danger("Horari amb dni $dni no té canvis");
        }

        if ($redirect) {
            return back();
        }
        else {
            return $correcto;
        }
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function changeTableAll(){
        $correctos = 0;

        foreach (Profesor::select('dni')->Activo()->get() as $profe) {
            $correctos += $this->changeTable($profe->dni,false);
        }
        Alert::success("He fet $correctos canvis d'horaris");
        return back();
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function changeIndex() {
        return view('horario.change');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function horarioCambiar($id = null){
        if ($id == null) {
            $id = AuthUser()->id;
        }
        $horario = Horario::HorarioSemanal($id);
        $profesor = Profesor::find($id);
        return view('horario.profesor-cambiar', compact('horario', 'profesor'));
    }

    /**
     *
     */
    protected function iniBotones()
    {
        $this->panel->setBotonera([],['edit']);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function index(){
        return $this->modificarHorario(Session::get('horarioProfesor'));
    }

    /**
     * @param $idProfesor
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    protected function modificarHorario($idProfesor){
        Session::forget('redirect'); //buida variable de sessió redirect ja que sols se utiliza en cas de direccio
        Session::put('horarioProfesor',$idProfesor);
        $this->titulo = ['quien' => Profesor::find($idProfesor)->fullName]; // paràmetres per al titol de la vista
        $this->iniBotones();
        return $this->grid(Horario::Profesor($idProfesor)->get());
    }
}
