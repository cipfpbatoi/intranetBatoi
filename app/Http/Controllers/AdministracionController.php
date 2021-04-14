<?php

/* clase : IntranetController
 * És la classe pare de tots els controladors amb el mètodes comuns a ells
 */

namespace Intranet\Http\Controllers;


use Intranet\Entities\Empresa;
use Illuminate\Support\Facades\Session;
use Intranet\Entities\Poll\Poll;
use Intranet\Entities\Poll\VoteAnt;
use Intranet\Entities\Programacion;
use DB;
use Styde\Html\Facades\Alert;
use Intranet\Entities\Profesor;
use Illuminate\Support\Facades\Storage;
use Intranet\Jobs\SendEmail;
use Illuminate\Support\Str;
use Intranet\Entities\AlumnoGrupo;
use Intranet\Entities\TipoExpediente;
use Intranet\Entities\Colaboracion;
use Intranet\Entities\Poll\Vote;
use Intranet\Entities\Fct;


/**
 * Class AdministracionController
 * @package Intranet\Http\Controllers
 */
class AdministracionController extends Controller
{
    const DIRECTORIO_GESTOR = 'gestor/Empresa/';
    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function simplifica()
    {
        if (Session::get('completa')) {
            Session::forget('completa');
        }
        else {
            Session::put('completa', 1);
        }
        return back();
    }

    /**
     * @param $lang
     * @return \Illuminate\Http\RedirectResponse
     */
    public function lang($lang)
    {
        Session::put('lang', $lang);
        return back();
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function allApiToken()
    {
        $remitente = ['nombre' => 'Intranet', 'email' => config('contacto.host.email')];
        foreach (Profesor::Activo()->get() as $profesor) {
            dispatch(new SendEmail($profesor->email, $remitente, 'email.apitoken', $profesor));
        }
        Alert::info('Correus enviats');
        return back();
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    protected function nuevoCursoIndex()
    {
        return view('nuevo.curso');
    }

    private function esborrarProgramacions(){
        Programacion::where('estado', 4)->delete();
        Programacion::where('curso', '!=', Curso())->update(['estado' => 4]);
    }

    private function esborrarEnquestes(){
        foreach (Poll::all() as $poll){
            if (!$poll->remains){
                $poll->delete();
            }
        }
    }
    private function ferVotsPermanents(){
        foreach (Vote::all() as $vote){
            if ($fct = Fct::find($vote->idOption1)){
                $newVote = new VoteAnt([
                    'option_id' => $vote->option_id,
                    'idColaboracion' => $fct->idColaboracion,
                    'value' => $vote->value,
                    'text' => $vote->text,
                    'curs' => CursoAnterior()
                ]);
                $newVote->save();
            }
        }
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function nuevoCurso()
    {
        Colaboracion::where('tutor','!=','')->update(['tutor'=>'']);
        Colaboracion::where('estado','>',1)->update(['estado' => 1]);
        Profesor::whereNotNull('fecha_baja')->update(['fecha_baja' => null]);

        $this->esborrarEnquestes();
        $this->ferVotsPermanents();

        foreach (AlumnoGrupo::with('Grupo')->with('Alumno')->get() as $algr) {
            if ($algr->curso == 2 && $algr->fol > 0) {
                $alumno = $algr->Alumno;
                $alumno->fol = 0;
                $alumno->save();
            }
        }


        $tables = ['actividades', 'comisiones', 'cursos', 'expedientes', 'faltas', 'faltas_itaca', 'faltas_profesores',
            'fcts', 'grupos_trabajo', 'guardias', 'horarios', 'incidencias', 'notifications', 'ordenes_trabajo', 'reservas',
            'resultados', 'reuniones', 'tutorias_grupos', 'activities','alumno_resultados','alumnos_grupos','polls'];
        foreach ($tables as $tabla) {
            DB::table($tabla)->delete();
        }
        $this->esborrarProgramacions();

        return back();
    }

    /**
     * @param $fichero
     * @param $enlace
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function help($fichero, $enlace)
    {
        return view('intranet.readme', ['elemento' => mdFind($fichero, $enlace)]);
    }

    /**
     * @param $version_antigua
     */
    public static function exe_actualizacion($version_antigua)
    {
        foreach (config('constants.version') as $version) {
            if ($version > $version_antigua) {
                AdministracionController::$version();
            }
        }
    }

    /**
     *
     */
    public static function v1_0()
    {
        Alert::info('Version 1.0');
    }

    /**
     *
     */
    public static function v1_1()
    {
        Alert::info('Version 1.1');
    }

    /**
     *
     */
    public static function v1_2()
    {
        Alert::info('Version 1.2');
    }

    /**
     *
     */
    public static function v1_3_4()
    {
        $a = new TipoExpediente();
        $a->id = 1;
        $a->titulo = 'Baixa Inasistència';
        $a->rol = 17;
        $a->save();
        $a = new TipoExpediente();
        $a->id = 2;
        $a->titulo = 'Pèrdua Avaluació Continua';
        $a->rol = 3;
        $a->save();
        $a = new TipoExpediente();
        $a->id = 3;
        $a->titulo = "Remisió al departament d'Orientació";
        $a->rol = 17;
        $a->orientacion = 1;
        $a->save();
        $a = new TipoExpediente();
        $a->id = 4;
        $a->titulo = "Part d'amonestació";
        $a->rol = 3;
        $a->orientacion = 0;
        $a = new TipoExpediente();
        $a->titulo = "Informe d'exempció FCT";
        $a->id = 5;
        $a->rol = 3;
        $a->orientacion = 0;
        $a->informe = 1;
        $a->save();
    }

    public static function v2_0(){
        Alert::info('Version 2.0');
    }
    public static function v2_01(){
        // partxe per actualitzar professors sense token

        $remitente = ['nombre' => 'Intranet', 'email' => config('contacto.host.email')];
        $profesores = Profesor::where('api_token','')->get();

        foreach ($profesores as $profesor){
            $profesor->api_token = Str::random(60);
            $profesor->save();
            dispatch(new SendEmail($profesor->email, $remitente, 'email.apitoken', $profesor));

        }

    }

    public function importaAnexoI(){
        $canvis = 0;
        $nous = 0;
        $malament = 0;
        foreach (Empresa::all() as $elemento){
            if (isset($elemento->fichero)&&strpos($elemento->fichero,'2018-2019')&&(file_exists(storage_path('/app/'.$elemento->fichero)))){
                Storage::put(self::DIRECTORIO_GESTOR.$elemento->cif.'.pdf',Storage::get($elemento->fichero));
                $elemento->fichero = self::DIRECTORIO_GESTOR.$elemento->cif.'.pdf';
                $elemento->save();
                $canvis++;
            }
            else {
                if (file_exists(storage_path('app/'.self::DIRECTORIO_GESTOR . $elemento->cif.'.pdf'))) {
                    $elemento->fichero = self::DIRECTORIO_GESTOR . $elemento->cif.'.pdf';
                    $elemento->save();
                    $nous++;
                } else {
                    $elemento->fichero = '';
                    $elemento->save();
                    $malament++;
                }
            }

        }
        Alert::info($canvis.' canviats,'.$nous.' nous,'.$malament.' esborrats');
        return back();
    }
    
    
}
