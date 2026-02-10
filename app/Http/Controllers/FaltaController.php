<?php

namespace Intranet\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Intranet\UI\Botones\BotonImg;
use Intranet\Entities\AlumnoFct;
use Intranet\Entities\Asistencia;
use Intranet\Entities\Expediente;
use Intranet\Entities\Falta;
use Intranet\Entities\Grupo;
use Intranet\Entities\Horario;
use Intranet\Entities\Profesor;
use Intranet\Entities\Programacion;
use Intranet\Entities\Resultado;
use Intranet\Entities\Reunion;
use Intranet\Http\Traits\Autorizacion;
use Intranet\Http\Traits\Imprimir;
use Intranet\Services\Notifications\AdviseTeacher;
use Intranet\Services\Notifications\ConfirmAndSend;
use Intranet\Services\General\StateService;
use Jenssegers\Date\Date;
use function PHPUnit\Framework\isEmpty;


/**
 * Class FaltaController
 * @package Intranet\Http\Controllers
 */
class FaltaController extends IntranetController
{

    use Imprimir, Autorizacion;

    /**
     * @var string
     */
    protected $perfil = 'profesor';
    /**
     * @var string
     */
    protected $model = 'Falta';
    /**
     * @var array
     */
    protected $gridFields = ['id', 'desde', 'hasta', 'motivo', 'situacion','observaciones'];
    /**
     * @var bool
     */
    protected $modal = true;

    /**
     *
     */
    protected function iniBotones()
    {
        $this->panel->setBotonera(['create']);
        $this->panel->setBoton('grid', new BotonImg('falta.delete', ['where' => ['estado', '==', '0']]));
        $this->panel->setBoton('grid', new BotonImg('falta.edit', ['where' => ['estado', '<', '3']]));
        $this->panel->setBoton('grid', new BotonImg('falta.init', ['where' => ['estado', '==', '0']]));
        $this->panel->setBoton('grid', new BotonImg('falta.document', ['where' => ['fichero', '!=', '']]));
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {

        $request->baja = isset($request->baja)?$request->baja:0;
        if ($request->baja) {
            DB::transaction(function () use ($request) {
                $request->hora_ini = null;
                $request->hora_fin = null;
                $request->hasta = '';
                $request->dia_completo = 1;
                $request->estado = 5;
                self::tramitaBajaProfesor($request->idProfesor, $request->desde);
                parent::realStore($request);
            });
        } else {
            $diaCompleto = isset($request->dia_completo)  ? 1 : null;
            $request->hora_ini = $diaCompleto ? null : $request->hora_ini;
            $request->hora_fin = $diaCompleto ? null : $request->hora_fin;
            $request->hasta = esMayor($request->desde, $request->hasta) ? $request->desde : $request->hasta;
            $id = parent::realStore($request);
            if (UserisAllow(config('roles.rol.direccion'))) {
                $this->init($id);
            } else {
                // si es direcció autoritza
                return ConfirmAndSend::render($this->model, $id);
            }
        }
        return $this->redirect();
    }

    

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {

        $diaCompleto = $request->has('dia_completo') ? 1 : 0;

        $request->merge([
            'hora_ini' => $diaCompleto ? null : $request->hora_ini,
            'hora_fin' => $diaCompleto ? null : $request->hora_fin,
            'hasta' => esMayor($request->desde, $request->hasta) ? $request->desde : $request->hasta,
        ]);

        $elemento = Falta::find(parent::realStore($request, $id));

        if ($elemento->estado == 1 && $elemento->fichero) {
            $staSer = new StateService($elemento);
            $staSer->putEstado(2);
        }

        return $this->redirect();
    }

    protected function createWithDefaultValues($default = [])
    {
        $data = new Date('today');
        return new Falta(['desde'=>$data,'hasta'=>$data,'idProfesor'=>AuthUser()->dni]);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function init($id)
    {
        $elemento = Falta::findOrFail($id);
        if (esMayor($elemento->desde, Hoy('Y/m/d'))) {
            AdviseTeacher::sendEmailTutor($elemento);
        }
        $stSrv = new StateService($elemento);
        if ($elemento->fichero) {
            $stSrv->putEstado(2);
        } else {
            $stSrv->putEstado(1);
        }
        return $this->redirect();
    }


    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function alta($id)
    {
        $elemento = Falta::findOrFail($id);
        DB::transaction(function () use ($elemento) {
            $elemento->estado = 3;
            $elemento->hasta = new Date();
            $elemento->baja = 0;
            $elemento->save();
            // quita la  baja del profesor
            self::tramitaAltaProfesor($elemento->idProfesor);
        });
        return back()->with('pestana', $elemento->estado);
    }

    /**
     * @param $idProfesor
     * @param $fecha
     */
    private static function tramitaBajaProfesor($idProfesor, $fecha)
    {
        $profe = Profesor::find($idProfesor);
        $profe->fecha_baja = new Date($fecha);
        $profe->save();
    }

    /**
     * @param $idProfesor
     */
    private static function tramitaAltaProfesor($idProfesor)
    {
        DB::transaction(function () use ($idProfesor) {
            $original = Profesor::find($idProfesor);
            $original->fecha_baja = null;
            $profesor = $original;
            while ($profesor->Sustituye) {
                self::changeWithSubstitute($original, $profesor->Sustituye);
                $profesor = $profesor->Sustituye;
            }
            $original->save();
        });
    }

    /**
     * @param $profesorAlta
     * @param $sustituto
     */
    private static function changeWithSubstitute($profesorAlta, $sustituto)
    {
            //canvi d'horari
            if (Horario::profesor($profesorAlta->dni)->count()==0) {
                Horario::where('idProfesor', $sustituto->dni)->update(['idProfesor'=> $profesorAlta->dni]);
            } else {
                Horario::where('idProfesor', $sustituto->dni)->delete();
            }

            // asistència a reunions
            foreach (Asistencia::where('idProfesor', $sustituto->dni)->get() as $asistencia) {
                self::markAssistenceMeetings($profesorAlta->dni, $asistencia);
            }
            // tota la feina del substitut pasa al subtituit
            Reunion::where('idProfesor', $sustituto->dni)->update(['idProfesor'=>$profesorAlta->dni]);
            Grupo::where('tutor', $sustituto->dni)->update(['tutor'=>$profesorAlta->dni]);
            Programacion::where('profesor', $sustituto->dni)->update(['profesor' => $profesorAlta->dni]);
            Expediente::where('idProfesor', $sustituto->dni)->update(['idProfesor' => $profesorAlta->dni]);
            Resultado::where('idProfesor', $sustituto->dni)->update(['idProfesor' => $profesorAlta->dni]);
            AlumnoFct::where('idProfesor', $sustituto->dni)->update(['idProfesor' => $profesorAlta->dni]);

            $sustituto->sustituye_a = ' ';
            $sustituto->activo = 0;
            $sustituto->save();

    }

    /**
     * @param $dniProfesor
     * @param $meeting
     */
    private static function markAssistenceMeetings($dniProfesor, $meeting)
    {
        if (Asistencia::where('idProfesor', $dniProfesor)->where('idReunion', $meeting->idReunion)->count() == 0) {
            Reunion::find($meeting->idReunion)->profesores()->syncWithoutDetaching([$dniProfesor=>['asiste'=>0]]);
        }
    }
}
