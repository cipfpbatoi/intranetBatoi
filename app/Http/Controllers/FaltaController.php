<?php

namespace Intranet\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Intranet\Botones\BotonImg;
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
use Intranet\Services\AdviseTeacher;
use Intranet\Services\ConfirmAndSend;
use Intranet\Services\GestorService;
use Intranet\Services\StateService;
use Carbon\Carbon;


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
     * @param Request $request
     * @return mixed
     */
    private static function findElements($desde, $hasta)
    {
        return Falta::where([
            ['estado', '>', '0'],
            ['estado', '<', '4'],
            ['desde', '>=', $desde],
            ['desde', '<=', $hasta]
        ])
            ->orwhere([
                ['estado', '>', '0'],
                ['estado', '<', '4'],
                ['hasta', '>=', $desde],
                ['hasta', '<=', $hasta]
            ])
            ->orwhere([['estado', '=', '5'],
                ['desde', '<=', $hasta]])
            ->orderBy('idProfesor')
            ->orderBy('desde')
            ->get();
    }

    /**
     * @return array
     */
    private static function nameFile():string
    {
        return 'gestor/' . Curso() . '/informes/' . 'Falta' .  Carbon::parse() . '.pdf';
    }


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
            $diaCompleto = $request->dia_completo == '' ? '0' : '1';
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
        $request->dia_completo = isset($request->dia_completo)?1:0;
        $request->hora_ini = $request->dia_completo ? null : $request->hora_ini;
        $request->hora_fin = $request->dia_completo ? null : $request->hora_fin;
        $request->hasta = esMayor($request->desde, $request->hasta) ? $request->desde : $request->hasta;
        $elemento = Falta::find(parent::realStore($request, $id));
        if ($elemento->estado == 1 && $elemento->fichero) {
            $staSer = new StateService($elemento);
            $staSer->putEstado(2);
        } // si estava enviat i he pujat fitxer
        return $this->redirect();
    }

    protected function createWithDefaultValues($default = [])
    {
        $data =  Carbon::parse('tomorrow');
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
            $elemento->hasta =  Carbon::parse();
            $elemento->baja = 0;
            $elemento->save();
            // quita la  baja del profesor
            self::tramitaAltaProfesor($elemento->idProfesor);
        });
        return back()->with('pestana', $elemento->estado);
    }

    /**
     * @param $hasta
     */
    private static function markPrinted($hasta)
    {
        foreach (Falta::where([
            ['estado', '>', '0'],
            ['estado', '<', '4'],
            ['hasta', '<=', $hasta]
        ])->get() as $elemento) {
            $staSer = new StateService($elemento);
            $staSer->_print();
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public static function printReport($request)
    {
        $desde =  Carbon::parse($request->desde);
        $hasta =  Carbon::parse($request->hasta);
        if ($request->mensual != 'on') {
            return self::hazPdf(
                "pdf.comunicacioAbsencia",
                Falta::where('estado', '>', '0')
                ->where('estado', '<', '5')
                ->whereBetween('desde', [$desde, $hasta])
                ->orWhereBetween('hasta', [$desde, $hasta])
                ->orwhere([['estado', '=', '5'], ['desde', '<=', $hasta]])
                ->orderBy('idProfesor')
                ->orderBy('desde')
                ->get()
            )->stream();
        }

        $nomComplet = self::nameFile();
        $gestor = new GestorService();
        $doc = $gestor->save([
                'fichero' => $nomComplet,
                'tags' => "Ausència Ausencia Llistat listado Professorado Profesorat Mensual"
        ]);

        $elementos = self::findElements($desde, $hasta);
        self::markPrinted($hasta);
        StateService::makeLink($elementos, $doc);

        return self::hazPdf("pdf.faltas", $elementos)
            ->save(storage_path('/app/' . $nomComplet))
            ->download($nomComplet);

    }


    /**
     * @param $idProfesor
     * @param $fecha
     */
    private static function tramitaBajaProfesor($idProfesor, $fecha)
    {
        $profe = Profesor::find($idProfesor);
        $profe->fecha_baja =  Carbon::parse($fecha);
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
