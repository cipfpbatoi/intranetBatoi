<?php

/* clase : IntranetController
 * És la classe pare de tots els controladors amb el mètodes comuns a ells
 */

namespace Intranet\Http\Controllers;


use Illuminate\Support\Facades\Http;
use Intranet\Entities\Articulo;
use Intranet\Entities\ArticuloLote;
use Intranet\Entities\Espacio;
use Intranet\Entities\Material;
use Intranet\Entities\Lote;
use Intranet\Entities\Documento;
use Intranet\Entities\Empresa;
use Illuminate\Support\Facades\Session;
use Intranet\Entities\Grupo;
use Intranet\Entities\Poll\Poll;
use Intranet\Entities\Poll\VoteAnt;
use Intranet\Entities\Programacion;
use DB;
use Intranet\Entities\Departamento;
use Styde\Html\Facades\Alert;
use Intranet\Entities\Profesor;
use Illuminate\Support\Facades\Storage;
use Intranet\Jobs\SendEmail;
use Intranet\Entities\AlumnoGrupo;
use Intranet\Entities\Colaboracion;
use Intranet\Entities\Poll\Vote;
use Intranet\Entities\Fct;
use Illuminate\Http\Request;



/**
 * Class AdministracionController
 * @package Intranet\Http\Controllers
 */
class AdministracionController extends Controller
{
    const DIRECTORIO_GESTOR = 'gestor/Empresa/';

/*
    public function actualizaLang(){
        $valencia = config('messages');
        $angles = config('messagesAngles');
        foreach ($valencia as $keyArray => $Array){
            foreach ($Array as $key => $value){
                if (!isset($angles[$keyArray][$key])){
                    $angles[$keyArray][$key] = $value;
                }
            }
        }
        $fp = fopen('message.txt', 'w');
        fwrite($fp, var_export($angles, true));
        fclose($fp);
    }
*/
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

    public function cleanCache()
    {
        Alert::info(system('php ./../artisan cache:clear'));
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
        Programacion::where('curso', '!=', Curso())->delete();
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
        Fct::where('asociacion','!=',3)->delete();
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
        foreach (Grupo::all() as $grupo){
            $grupo->fol = 0;
            $grupo->save();
        }


        $tables = ['actividades', 'comisiones', 'cursos', 'expedientes', 'faltas', 'faltas_itaca', 'faltas_profesores',
            'grupos_trabajo', 'guardias', 'horarios', 'incidencias', 'notifications', 'ordenes_trabajo', 'reservas',
            'resultados', 'reuniones', 'tutorias_grupos', 'activities','alumno_resultados','alumnos_grupos','polls','autorizaciones'];
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


    public static function v2_01(){
        // partxe per actualitzar professors sense token

        Alert::info('Version 2.01');

        /*
        $remitente = ['nombre' => 'Intranet', 'email' => config('contacto.host.email')];
        $profesores = Profesor::where('api_token','')->get();

        foreach ($profesores as $profesor){
            $profesor->api_token = Str::random(60);
            $profesor->save();
            dispatch(new SendEmail($profesor->email, $remitente, 'email.apitoken', $profesor));

        }
         */

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


    public static function v2_35(){
        Alert::info('Version 2.35');
        foreach (Profesor::whereNull('changePassword')->get() as $profesor){
                $profesor->email = emailConselleria($profesor->nombre,$profesor->apellido1,$profesor->apellido2);
                $profesor->save();
        }
    }




    private static function findProfesor($nombre){
        foreach (Profesor::all() as $profesor){
            if ($profesor->fullName == $nombre){
                return $profesor->dni;
            }

        }
        return false;
    }

    public function showDoor(){
        $doors = Espacio::whereNotNull('dispositivo')->get();
        return view('espai.show',compact('doors'));
    }

    public function secure(Request $request){
        $user = env('USER_DOMOTICA');
        $pass =  env('PASS_DOMOTICA');
        if (esrol(AuthUser()->rol,config('roles.rol.administrador'))){
            $link = str_replace('{dispositivo}',$request->dispositivo,config('variables.ipDomotica')).'/secure';
            $response = Http::withBasicAuth($user, $pass)->accept('application/json')->post($link,['args'=>[]]);
            if ($response->successful()){
                $missatge = 'Porta tancada';
            } else {
                $missatge = "No s'ha pogut tancar la porta: ".$response->status();
            }
        } else {
            $missatge = 'Usuari no autoritzat';
        }
        $doors = Espacio::whereNotNull('dispositivo')->get();
        return view('espai.show',compact('missatge','doors'));
    }
    
}
