<?php

namespace Intranet\Http\Controllers;

use Intranet\Entities\Profesor;
use Intranet\Entities\Horario;
use Intranet\Entities\Hora;
use Intranet\Entities\Actividad;
use Intranet\Entities\Comision;
use Intranet\Entities\Falta;
use Styde\Html\Facades\Alert;
use Jenssegers\Date\Date;
use Intranet\Botones\BotonBasico;
use DateTime;

class PanelGuardiaController extends BaseController
{

    protected $perfil = 'profesor';
    protected $model = 'Horario';
    protected $gridFields = ['aula', 'Profesor', 'XGrupo', 'donde'];
    protected $titulo = ['quien' => 'Guardia'];

    private function coincideHorario($elemento,$sesion){
        if (esMismoDia($elemento->desde, $elemento->hasta)) {
            if (isset($elemento->dia_completo)) return true;
            if (isset($elemento->hora_ini))
                $horas = Hora::horasAfectadas($elemento->hora_ini, $elemento->hora_fin);
            else
                $horas = Hora::horasAfectadas(hora($elemento->desde), hora($elemento->hasta));
            if ($sesion >= $horas[0] && $sesion <= $horas[count($horas)-1]) return true;
            
        } else return true;
        return false;
    }
    
    protected function iniBotones(){
        $this->panel->setBoton('index', new BotonBasico('guardia.',['text'=>'Tornar Guàrdia']));
    }
    
    public function search()
    {
        $idProfesor = AuthUser()->dni;
        $sesion = sesion(hora());
        $dia_semana = nameDay(Hoy());
        

        $guardia = Horario::distinct()
                ->Profesor($idProfesor)
                ->Dia($dia_semana)
                ->where('sesion_orden', $sesion)
                ->where('ocupacion', config('constants.codigoGuardia'))
                ->first();
        if ($guardia) {
            $ahora = Horario::distinct()
                    ->Dia($dia_semana)
                    ->where('sesion_orden', $sesion)
                    ->Lectivos()
                    ->whereNotNull('idGrupo')
                    ->whereNull('ocupacion')
                    ->get();


            $actividades = Actividad::Dia(Hoy())
                    ->where('fueraCentro', '=', 1)
                    ->get();
            foreach ($actividades as $actividad)
                if ($this->coincideHorario($actividad, $sesion)) {
                    foreach ($actividad->grupos as $grupo) {
                        $horario = $ahora->firstWhere('idGrupo', $grupo->codigo);
                        if ($horario) $horario->donde = "Extraescolar Grup";
                    }
                    foreach ($actividad->profesores as $profesor) {
                        $horario = $ahora->firstWhere('idProfesor', $profesor->dni);
                        if ($horario && !isset($horario->donde))
                            $horario->donde = "Extraescolar Profesor";
                    }
                }
            
            foreach ($ahora as $horario)
                if (!isset($horario->donde)) {
                    $profesor = Profesor::find($horario->idProfesor);
                    if ($profesor->ahora == 'A casa') {
                        $comision = Comision::Dia(Hoy())->where('idProfesor', $profesor->dni)->first();
                        if ($comision && $this->coincideHorario($comision, $sesion))
                            $horario->donde = 'En comisión de servicio';
                        else {
                            $falta = Falta::Dia(Hoy())->where('idProfesor', $profesor->dni)->first();
                            if ($falta && $this->coincideHorario($falta, $sesion))
                                $horario->donde = 'Comunica Ausencia';
                            else
                                $horario->donde = 'A casa';
                        }
                    } else
                        $horario->donde = $profesor->ahora;
                }
            return $ahora;
        } else {
            Alert('No estas de guardia ara');
            return [];
        }
    }

}
