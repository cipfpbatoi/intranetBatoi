<?php

namespace Intranet\Http\Controllers;
use Intranet\Entities\Documento;
use Intranet\Entities\Incidencia;
use Intranet\Entities\Profesor;
use Intranet\Entities\Modulo_grupo;
use Intranet\Entities\Programacion;
use Intranet\Entities\Comision;
use Illuminate\Support\Facades\Session;
use Intranet\Entities\Poll\Poll;
use Intranet\Entities\Poll\PPoll;
use Intranet\Entities\Poll\Vote;
use Intranet\Entities\TipoReunion;
use Intranet\Entities\AlumnoFctAval;
use Intranet\Entities\Grupo;
use Intranet\Entities\Reunion;
use function PHPUnit\Framework\isNull;


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
            if (($teacher->rol % $rol == 0) && method_exists($this, $nomRol)) {
                $avisos[$nomRol] = $this::$nomRol();
            }
        }
        Session::forget('redirect'); //buida variable de sessió redirect ja que sols se utiliza en cas de direccio
        return view('notification.fiCurs', compact('avisos'));

    }

    private static function profesor(){
        $avisos = [];

        self::lookForMyResults($avisos);
        self::lookforMyPrograms($avisos);
        self::lookUnPaidBills($avisos);

        if (AuthUser()->xdepartamento == 'Fol' ){

            self::lookForCheckFol($avisos);
        }
        return $avisos;
    }

    private static function mantenimiento()
    {
        $avisos = [];

        self::lookForIssues($avisos);

        return $avisos;
    }

    private static function direccion()
    {
        $avisos = [];

        self::lookForActesPendents($avisos);

        return $avisos;
    }

    private static function jefe_dpto()
    {
        $avisos = [];

        self::lookforInformsDepartment($avisos);

        return $avisos;
    }

    private static function tutor()
    {
        $avisos = [];

        self::lookAtPollsTutor($avisos);
        self::lookAtActasUpload($avisos);
        self::lookAtFctsProjects($avisos);
        self::lookAtQualitatUpload($avisos);
        return $avisos;
    }

    private static function lookForCheckFol(&$avisos)
    {
        foreach (Grupo::misGrupos()->get() as $grupo) {
            if ($grupo->fol == 0) {
                $avisos[self::DANGER][] = "FOL Grupo no revisado : ".$grupo->nombre;
            }
        }
    }

    private static function lookForIssues(&$avisos) {
        foreach (Incidencia::where('responsable', AuthUser()->dni)->where('estado', '<', 3)->where('estado','>',0)->get() as $incidencia) {
            $avisos[self::DANGER][] = "Incidencia no resolta : ".$incidencia->descripcion;
        }
    }

    private static function lookForActesPendents(&$avisos)
    {
        foreach (Grupo::where('acta_pendiente', '>', 0)->get() as $grupo) {
            $avisos[self::DANGER][] = "Acta pendent del grup : ".$grupo->nombre;
        }

    }

    private static function lookforInformsDepartment(&$avisos)
    {
        if (Documento::where('propietario', AuthUser()->FullName)->where('tipoDocumento', 'Acta')
            ->where('curso', Curso())->where('descripcion', 'Informe Trimestral')->count()==3) {
            $avisos[self::SUCCESS][] = "Informes trimestrals fets";
        }
        else {
            $avisos[self::DANGER][] = "Falta informe trimestral";
        }
    }

    private static function lookAtQualitatUpload(&$avisos)
    {
        if (esRol(AuthUser()->rol,31)){
            if (Documento::where('propietario', AuthUser()->FullName)->where('tipoDocumento', 'FCT')
                ->where('curso', Curso())->first()) {
                $avisos[self::SUCCESS][] = "Documentació FCT correcta";
            }
            else {
                $avisos[self::DANGER][] = "Falta pujar documentacio entrevistes FCT";
            }
        }
    }

    private static function lookAtActasUpload(&$avisos)
    {
        foreach ( config('auxiliares.reunionesControlables') as $tipo => $howManyNeeded) {
            if ($howManyNeeded){
                $howManyAre = Reunion::Convocante()->Tipo($tipo)->Archivada()->count();
                if ($howManyAre >= $howManyNeeded) {
                    $avisos[self::SUCCESS][] = "Acta ".TipoReunion::find($tipo)->vliteral." Arxivada";
                }
                else {
                    $avisos[self::DANGER][] = "Falten Actes de ".TipoReunion::find($tipo)->vliteral." per arxivar";
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
            $quests = $modelo::loadPoll(self::loadPreviousVotes($poll))??[];
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

            if (( !isNull($fctAval->calificacion)  || $fctAval->actas < 2) && $fctAval->asociacion == 1){
                $avisos[self::DANGER][] = "Fct de l'alumne ".$fctAval->Nombre.' no avaluada';
            }

            if (($fctAval->calProyecto >0 && $grupo->proyecto) &&
                (Documento::where('tipoDocumento','Proyecto')
                        ->where('curso',curso())
                        ->where('propietario',$fctAval->fullName)
                        ->count() == 0))
            {
                $avisos[self::DANGER][] = "Projecte de l'alumne ".$fctAval->fullName.' no existeix';
            }

        }
    }


    private static function loadPreviousVotes($poll){
        return hazArray(Vote::where('user_id','=', AuthUser()->dni)
            ->where('idPoll', $poll->id)
            ->get(),'idOption1','idOption1');
    }






    private static function lookForMyResults(&$avisos){
        foreach (Modulo_grupo::misModulos() as $modulo){
            if (!$modulo->resultados->where('evaluacion',3)){
                $avisos[self::DANGER][] = "Falta resultats finals del modul ".$modulo->literal;
            }
            else{
                $avisos[self::SUCCESS][] = "Resultats finals del modul ".$modulo->literal;
            }
        }
    }

    private static function lookforMyPrograms(&$avisos){
        foreach (Programacion::misProgramaciones()->get() as $programacion){
            if (is_null($programacion->propuestas ) || $programacion->propuestas == ''){
                $avisos[self::DANGER][] = "Falta avaluació programació del modul ".$programacion->descripcion;
            }
            else{
                $avisos[self::SUCCESS][] = "Hi ha avaluació programació del modul ".$programacion->descripcion;
            }
        }
    }

    private static function lookUnPaidBills(&$avisos){
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
    }




    
}
