<?php

namespace Intranet\Http\Controllers;

use Intranet\Botones\BotonImg;
use Intranet\Entities\Falta_profesor;
use Intranet\Entities\Profesor;
use Intranet\Entities\Actividad;
use Intranet\Entities\Comision;
use Intranet\Entities\Falta;
use Illuminate\Support\Facades\Session;
use Intranet\Services\FitxatgeService;
use Jenssegers\Date\Date;
use Intranet\Entities\Horario;


class PanelPresenciaController extends BaseController
{
    
    protected $model = 'Profesor';
    protected $gridFields = ['Xdepartamento', 'NameFull', 'email'];
    protected $vista = ['index' => 'llist.ausencia'];
    
    
    public function indice($dia = null)
    {
        Session::forget('redirect'); //buida variable de sessió redirect ja que sols se utiliza en cas de direccio
        $dia = $dia ? $dia : Hoy();
        $fdia = new Date($dia);
        $this->panel->dia = $fdia->toDateString();
        $this->panel->anterior = $fdia->subDay()->toDateString();
        $this->panel->posterior = $fdia->addDays(2)->toDateString();
        $this->panel->setBoton('grid', new BotonImg('fichar.delete', [], 'direccion', $this->panel->dia));
        return $this->grid(Profesor::whereIn('dni', self::noHanFichado($dia))->get());
    }


    public function deleteDia($usuario, $dia, FitxatgeService $fitxatgeService)
    {
        $fitxatgeService->fitxaDiaManual($usuario, $dia);
        return back();
    }

    public static function noHanFichado($dia)
    {
        $profesores = Profesor::select('dni')->Activo()->get();
        
        // mira qui no ha fitxat
        $noHanFichado = [];
        foreach ($profesores as $profesor) {
            if (Falta_profesor::haFichado($dia, $profesor->dni)->count() == 0) {
                if (Horario::Profesor($profesor->dni)->Dia(nameDay(new Date($dia)))->count() > 1) {
                    $noHanFichado[$profesor->dni] = $profesor->dni;
                }
            }
        }


        // comprova que no estigues d'activitat
        $actividades = Actividad::Dia($dia)->where('fueraCentro','=',1)->get();
        foreach ($actividades as $actividad) {
            foreach ($actividad->profesores as $profesor) {
                if (in_array($profesor->dni, $noHanFichado)) {
                    unset($noHanFichado[$profesor->dni]);
                }
            }
        }

        // comprova que no està de comissió
        $comisiones = Comision::Dia($dia)->get();
        foreach ($comisiones as $comision) {
            if (in_array($comision->idProfesor, $noHanFichado)) {
                unset($noHanFichado[$comision->idProfesor]);
            }
        }

        // compova que no tinga falta
        $faltas = Falta::Dia($dia)->get();
        foreach ($faltas as $falta) {
            if (in_array($falta->idProfesor, $noHanFichado)) {
                unset($noHanFichado[$falta->idProfesor]);
            }
        }
        
        return $noHanFichado;
    }
    
}
