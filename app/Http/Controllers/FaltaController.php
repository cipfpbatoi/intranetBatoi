<?php

namespace Intranet\Http\Controllers;

use Intranet\Entities\Falta;
use Intranet\Entities\Profesor;
use Intranet\Entities\Horario;
use Intranet\Entities\Asistencia;
use Intranet\Entities\Reunion;
use Intranet\Entities\Grupo;
use Intranet\Entities\Programacion;
use Intranet\Entities\Expediente;
use Intranet\Entities\Resultado;

use Intranet\Jobs\SendEmail;
use Intranet\Services\GestorService;
use Intranet\Services\StateService;
use Jenssegers\Date\Date;
use \DB;
use Intranet\Botones\BotonImg;
use Illuminate\Http\Request;
use PDF;
use Styde\Html\Facades\Alert;

/**
 * Class FaltaController
 * @package Intranet\Http\Controllers
 */
class FaltaController extends IntranetController
{

    use traitImprimir,
        traitNotificar,
        traitAutorizar;

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
    private static function findElements($desde,$hasta)
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
        return 'gestor/' . Curso() . '/informes/' . 'Falta' . new Date() . '.pdf';
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
            DB::transaction(function() use ($request){
                $request->hora_ini = null;
                $request->hora_fin = null;
                $request->hasta = '';
                $request->dia_completo = 1;
                $request->estado = 5;
                self::tramitaBajaProfesor($request->idProfesor, $request->desde);
                parent::realStore($request);
            });
        } else {
            $dia_completo = $request->dia_completo == '' ? '0' : '1';
            $request->hora_ini = $dia_completo ? null : $request->hora_ini;
            $request->hora_fin = $dia_completo ? null : $request->hora_fin;
            $request->hasta = esMayor($request->desde, $request->hasta) ? $request->desde : $request->hasta;
            $id = parent::realStore($request);
            if (UserisAllow(config('roles.rol.direccion'))) {
                $this->init($id);
            } // si es direcció autoritza
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
        $elemento = Falta::find(parent::realStore($request,$id));
        if ($elemento->estado == 1 && $elemento->fichero) {
            Falta::putEstado($id,2);
        } // si estava enviat i he pujat fitxer
        return $this->redirect();
    }

    protected function createWithDefaultValues( $default = [])
    {
        $data = new Date('tomorrow');
        return new Falta(['desde'=>$data,'hasta'=>$data,'idProfesor'=>AuthUser()->dni]);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function init($id)
    {
        $elemento = Falta::findOrFail($id);
        $this->avisaTutor($elemento);
        $stSrv = new StateService($elemento);
        if ($elemento->fichero) {
            $stSrv->putEstado(2);
        } else {
            $stSrv->putEstado(1);
        }

        return $this->redirect();
    }
/**
    Covid avisa soles al tutor
    amb correu electrònic
*/
    protected function avisaTutor($elemento)
    {
        $idEmisor = $elemento->idProfesor;
        foreach ($this->gruposAfectados($elemento, $idEmisor)->toArray() as $grupos){
            foreach ($grupos as $item) {
                $grupo = Grupo::find($item);
                $correoTutor = $grupo->Tutor->Sustituye->email ?? $grupo->Tutor->email;
                $correoDireccion = 'faltes@cipfpbatoi.es';
                $remitente =  ['nombre'=>'Caporalia','email'=>'faltes@cipfpbatoi.es'];
                SendEmail::dispatch($correoTutor,$remitente, 'email.faltaProfesor', $elemento);
                SendEmail::dispatch($correoDireccion, $remitente, 'email.faltaProfesor', $elemento);
                Alert::info("Correos enviados a $item");
            }
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function alta($id)
    {
        $elemento = Falta::findOrFail($id);
        DB::transaction(function() use ($elemento){
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
     * @param $hasta
     */
    private static function markPrinted($hasta){
        foreach (Falta::where([
            ['estado', '>', '0'],
            ['estado', '<', '4'],
            ['hasta', '<=', $hasta]
        ])->get() as $elemento) {
            $elemento::_print($elemento->id);
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public static function printReport(Request $request)
    {
        $desde = new Date($request->desde);
        $hasta = new Date($request->hasta);
        if ($request->mensual != 'on') {
            return self::hazPdf("pdf.comunicacioAbsencia", Falta::where('estado', '>', '0')
                ->where('estado', '<', '5')
                ->whereBetween('desde', [$desde, $hasta])
                ->orWhereBetween('hasta', [$desde, $hasta])
                ->orwhere([['estado', '=', '5'],
                    ['desde', '<=', $hasta]])
                ->orderBy('idProfesor')
                ->orderBy('desde')
                ->get())->stream();
        }

        $nomComplet = self::nameFile();
        $gestor = new GestorService();
        $doc = $gestor->save(['fichero' => $nomComplet, 'tags' => "Ausència Ausencia Llistat listado Professorado Profesorat Mensual"]);

        $elementos = self::findElements($desde,$hasta);
        self::markPrinted($hasta);
        self::makeLink($elementos, $doc);

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
        $profe->fecha_baja = new Date($fecha);
        $profe->save();
    }

    /**
     * @param $idProfesor
     */
    private static function tramitaAltaProfesor($idProfesor){
        DB::transaction(function() use ($idProfesor) {
            $profesor = Profesor::find($idProfesor);
            $profesor->fecha_baja = null;
            $profesor->save();
            if ($profesor->Sustituye) {
                self::changeWithSubstitute($profesor,$profesor->Sustituye);
            }
        });
    }

    /**
     * @param $profesorAlta
     * @param $sustituto
     */
    private static function changeWithSubstitute($profesorAlta, $sustituto){

            //canvi d'horari
            if (Horario::profesor($profesorAlta->dni)->count()==0) {
                Horario::where('idProfesor',$sustituto->dni)->update(['idProfesor'=> $profesorAlta->dni]);
            } else {
                Horario::where('idProfesor',$sustituto->dni)->delete();
            }

            // asistència a reunions
            foreach (Asistencia::where('idProfesor',$sustituto->dni)->get() as $asistencia){
                self::markAssistenceMeetings($profesorAlta->dni,$asistencia);
            }
            // tota la feina del substitut pasa al subtituit
            Reunion::where('idProfesor',$sustituto->dni)->update(['idProfesor'=>$profesorAlta->dni]);
            Grupo::where('tutor',$sustituto->dni)->update(['tutor'=>$profesorAlta->dni]);
            Programacion::where('profesor',$sustituto->dni)->update(['profesor' => $profesorAlta->dni]);
            Expediente::where('idProfesor', $sustituto->dni)->update(['idProfesor' => $profesorAlta->dni]);
            Resultado::where('idProfesor', $sustituto->dni)->update(['idProfesor' => $profesorAlta->dni]);

            $sustituto->sustituye_a = ' ';
            $sustituto->activo = 0;
            $sustituto->save();

    }

    /**
     * @param $dniProfesor
     * @param $meeting
     */
    private static function markAssistenceMeetings($dniProfesor, $meeting){
        if (Asistencia::where('idProfesor', $dniProfesor)->where('idReunion', $meeting->idReunion)->count() == 0){
            Reunion::find($meeting->idReunion)->profesores()->syncWithoutDetaching([$dniProfesor=>['asiste'=>0]]);
        }
    }
}
