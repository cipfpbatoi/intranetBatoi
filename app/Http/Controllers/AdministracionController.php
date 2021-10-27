<?php

/* clase : IntranetController
 * És la classe pare de tots els controladors amb el mètodes comuns a ells
 */

namespace Intranet\Http\Controllers;


use Intranet\Entities\Articulo;
use Intranet\Entities\ArticuloLote;
use Intranet\Entities\Material;
use Intranet\Entities\Lote;
use Intranet\Entities\Documento;
use Intranet\Entities\Empresa;
use Illuminate\Support\Facades\Session;
use Intranet\Entities\Grupo;
use Intranet\Entities\Menu;
use Intranet\Entities\Poll\Poll;
use Intranet\Entities\Poll\VoteAnt;
use Intranet\Entities\Programacion;
use DB;
use Jenssegers\Date\Date;
use Styde\Html\Facades\Alert;
use Intranet\Entities\Profesor;
use Illuminate\Support\Facades\Storage;
use Intranet\Jobs\SendEmail;
use Illuminate\Support\Str;
use Intranet\Entities\AlumnoGrupo;
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
        Alert::info('Version 1.3.4');
    }

    public static function v2_0(){
        Alert::info('Version 2.0');
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


    public static function v2_04(){
        $menu = new Menu([]);
        $menu->nombre = 'importaemail';
        $menu->url = '/importEmail';
        $menu->rol = 11;
        $menu->menu = 'general';
        $menu->submenu = 'administracion';
        $menu->activo = 1;
        $menu->orden = 9;
        $menu->save();
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


    public static function v2_02()
    {
        Alert::info('Version 2.02');
        return ;
        $proyectos = Documento::where('tipoDocumento','Proyecto')->where('curso','2020-2021')->get();
        $grupos = $proyectos->groupBy('supervisor');
        foreach ($grupos as $grupo){
            $dni = self::findProfesor($grupo->first()->supervisor);
            if ($dni) {
                foreach ($grupo as $proyecto) {
                    $proyecto->ciclo = Grupo::QTutor($dni)->first()->Ciclo->ciclo ?? '';
                    $proyecto->save();
                }
            } else {
                Alert::danger('Profesor '.$grupo->first()->supervisor.' no trobat');
            }
        }
    }

    public static function v2_03()
    {
        Alert::info('Version 2.3');
        return ;
        $ordenadores = DB::table('ordenadores')->get();

       DB::transaction(function () use ($ordenadores){
           $lote = Lote::where('registre','2021-IMP')->first();
           $registre = '2021-IMP';
           if (!$lote){
               $lote = new Lote(['registre'=>$registre,'procedencia'=>1,'proveedor'=>'Inventario']);
               $lote->save();
           }

           $articulo = Articulo::where('descripcion','Ordenador Torre')->first();
           $articulo_lote = new ArticuloLote(['lote_id'=>$registre,'articulo_id'=>$articulo->id,'unidades'=>0]);
           $articulo_lote->save();
           $quantitat = 0;
           foreach ($ordenadores as $ordenador){
               $material = new Material(['descripcion'=>'Ordenador Torre','procedencia'=>1,'estado'=>1,'espacio'=>$ordenador->aula,'unidades'=>1,'inventariable'=>1,'articulo_lote_id'=>$articulo_lote->id,'nserieprov'=>$ordenador->serie]);
               $material->save();
               $quantitat++;
            }
           $articulo_lote->unidades = $quantitat;
           $articulo_lote->save();
        });
    }


    private static function findProfesor($nombre){
        foreach (Profesor::all() as $profesor){
            if ($profesor->fullName == $nombre){
                return $profesor->dni;
            }

        }
        return false;
    }
    
}
