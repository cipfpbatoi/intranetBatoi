<?php

namespace Intranet\Http\Controllers;
use Intranet\Entities\Documento;
use Intranet\Entities\Profesor;
use Intranet\Entities\Modulo_grupo;
use Intranet\Entities\Programacion;
use Intranet\Entities\Comision;
use Illuminate\Support\Facades\Session;
use Intranet\Entities\Poll\Poll;
use Intranet\Entities\Poll\PPoll;
use Intranet\Entities\Poll\Vote;
use Intranet\Entities\TipoReunion;
use Styde\Html\Facades\Alert;
use Intranet\Entities\AlumnoFctAval;
use Intranet\Entities\Grupo;
use Intranet\Entities\Reunion;




/**
 * Class PanelActaController
 * @package Intranet\Http\Controllers
 */
class PanelFinCursoController extends BaseController
{
    const DANGER = 'danger';
    const SUCCESS = 'success';

    /**
     * @var string
     */
    protected $perfil = 'profesor';
    /**
     * @var string
     */
    protected $model = 'Profesor';

    /**
     * @param null $grupo
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function index($profesor=null)
    {
        $avisos = [];
        $teacher = Profesor::find($profesor)??AuthUser();
        foreach (config('roles.rol') as $nomRol => $rol){
            if (($teacher->rol % $rol == 0) && method_exists($this,$nomRol)){
                $avisos[$nomRol] = $this::$nomRol();
            }
        }
        Session::forget('redirect'); //buida variable de sessió redirect ja que sols se utiliza en cas de direccio
        dd($avisos);
        if ($this->iniPestanas($grupo)){
            return $this->grid($this->search($grupo),$this->modal);
        }

    }

    private static function direccion(){
        return [];
    }

    private static function jefe_dpto(){
        return [];
    }

    private static function tutor(){
        $avisos = [];

        self::lookAtPollsTutor($avisos);
        self::lookAtFctsProjects($avisos);
        self::lookAtQualitatUpload($avisos);
        self::lookAtActasUpload($avisos);

        return $avisos;
    }

    private static function lookAtQualitatUpload(&$avisos){
        if (Documento::where('propietario', AuthUser()->FullName)->where('tipoDocumento', 'Qualitat')
            ->where('curso', Curso())->first()) {
            $avisos[self::SUCCESS][] = "Documentacio qualitat correcta";
        }
        else {
            $avisos[self::DANGER][] = "Falta pujar documentacio qualitat";
        }
    }
    private static function lookAtActasUpload(&$avisos){

            foreach ( config('auxiliares.reunionesControlables') as $tipo => $howManyNeeded) {
                if ($howManyNeeded){
                    $howManyAre = Reunion::Convocante()->Tipo($tipo)->Archivada()->count();
                    if ($howManyAre >= $howManyNeeded) {
                        $avisos[self::SUCCESS][] = "Acta ".TipoReunion::literal($tipo)." Artxivada";
                    }
                    else {
                        $avisos[self::DANGER][] = "Falten Actes de ".TipoReunion::literal($tipo)." per artxivar";
                    }

                }
            }

    }
    private static function lookAtPollsTutor(&$avisos){
        $ppols = hazArray(PPoll::where('what','Fct')->get(),'id','id');
        $polls = hazArray(Poll::whereIn('idPPoll',$ppols)->get(),'id','id');
        foreach ($polls as $id){
            $poll = Poll::find($id);
            $modelo = $poll->modelo;
            $quests = $modelo::loadPoll(self::loadPreviousVotes($poll));
            foreach ($quests as $fcts){
                foreach ($fcts as $fct){
                    $avisos[self::DANGER][] = "Falta omplir enquesta FCT centre ".$fct->Centro;
                }
            }
        }
    }
    private static function lookAtFctsProjects(&$avisos){
        $grupo = Grupo::QTutor()->first();
        $alumnes = AlumnoFctAval::select('idAlumno')->distinct()->misFcts()->esAval()->get()->toArray();
        foreach ($alumnes as $alumne){
            $fctAval = AlumnoFctAval::esAval()->where('idAlumno',$alumne['idAlumno'])->orderBy('idAlumno')->first();

            if ((!$fctAval->calificacion || $fctAval->acta < 2) && $fctAval->asociacion == 1){
                $avisos[self::DANGER][] = "Fct de l'alumne ".$fctAval->Nombre.' no avaluada';
            }

            if (($fctAval->calProyecto >0 && $grupo->proyecto) &&
                (Documento::where('tipoDocumento','Proyecto')
                        ->where('curso',curso())
                        ->where('propietario',$fctAval->Nombre)
                        ->count() == 0))
            {
                $avisos[self::DANGER][] = "Projecte de l'alumne ".$fctAval->Nombre.' no existeix';
            }
        }
    }

    private static function loadPreviousVotes($poll){
        return hazArray(Vote::where('user_id','=', AuthUser()->dni)
            ->where('idPoll', $poll->id)
            ->get(),'idOption1','idOption1');
    }


    private static function mantenimiento(){
        return [];
    }

    private function profesor(){
        $avisos = [];
        foreach (Modulo_grupo::misModulos() as $modulo){
            if (!$modulo->resultados->where('evaluacion',3)){
                $avisos[self::DANGER][] = "Falta resultats finals del modul ".$modulo->literal;
            }
            else{
                $avisos[self::SUCCESS][] = "Resultats finals del modul ".$modulo->literal;
            }
        }
        foreach (Programacion::misProgramaciones()->get() as $programacion){
            if (is_null($programacion->propuestas ) || $programacion->propuestas == ''){
                $avisos[self::DANGER][] = "Falta avaluació programació del modul ".$programacion->descripcion;
            }
            else{
                $avisos[self::SUCCESS][] = "Hi ha avaluació programació del modul ".$programacion->descripcion;
            }
        }
        if (Comision::Actual()->where('estado','<',4)
            ->where(function($query) {
                $query->where('comida','>',0)
                    ->orWhere('gastos','>',0)
                    ->orWhere('alojamiento','>',0)
                    ->orWhere('kilometraje','>',0);
            })->count()){
            $avisos['alert'][] = "Tens comissions de servei pendents de cobrar";
        }
        else{
            $avisos[self::SUCCESS][] = 'Comissions correctes';
        }
        return $avisos;
    }




    
}
