<?php

/* clase : IntranetController
 * És la classe pare de tots els controladors amb el mètodes comuns a ells
 */

namespace Intranet\Http\Controllers;

use Intranet\Entities\Empresa;
use Intranet\Http\Controllers\Controller;
use Intranet\Botones\Panel;
use Illuminate\Support\Facades\Session;
use Intranet\Entities\Programacion;
use Intranet\Entities\Modulo_ciclo;
use Intranet\Entities\Modulo_grupo;
use Intranet\Entities\Ciclo;
use Intranet\Entities\Resultado;
use DB;
use Intranet\Entities\Horario;
use Intranet\Entities\Grupo;
use Styde\Html\Facades\Alert;
use Intranet\Entities\Profesor;
use Illuminate\Support\Facades\Storage;
use Intranet\Entities\Menu;
use Intranet\Jobs\SendEmail;
use Intranet\Entities\Fct;
use Intranet\Entities\AlumnoFct;
use Intranet\Entities\FctColaborador;
use Intranet\Entities\Expediente;
use Intranet\Entities\Alumno;
use Intranet\Entities\TipoExpediente;

/**
 * Class AdministracionController
 * @package Intranet\Http\Controllers
 */
class AdministracionController extends Controller
{

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function simplifica()
    {
        if (Session::get('completa'))
            Session::forget('completa');
        else
            Session::put('completa', 1);
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
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    protected function deleteProgramacionIndex()
    {
        $cuantas = Programacion::where('estado', 3)->where('curso', '!=', Curso())->count();
        return view('programacion.deleteOld', compact('cuantas'));
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function deleteProgramacion()
    {
        Programacion::where('estado', 4)->delete();
        Programacion::where('curso', '!=', Curso())->update(['estado' => 4]);
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

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function nuevoCurso()
    {
        //$this->checkForeignKeys(false);
        $tables = ['actividades', 'comisiones', 'cursos', 'expedientes', 'faltas', 'faltas_itaca', 'faltas_profesores',
            'fcts', 'grupos_trabajo', 'guardias', 'horarios', 'incidencias', 'notifications', 'ordenes_trabajo', 'reservas',
            'resultados', 'reuniones', 'tutorias_grupos', 'activities','votes'];
        foreach ($tables as $tabla) {
            DB::table($tabla)->delete();
        }

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

    public function importaAnexoI(){
        $canvis = 0;
        $nous = 0;
        $malament = 0;
        foreach (Empresa::all() as $elemento){
            if (isset($elemento->fichero)&&strpos($elemento->fichero,'2018-2019')&&(file_exists(storage_path('/app/'.$elemento->fichero)))){
                Storage::put('gestor/Empresa/'.$elemento->cif.'.pdf',Storage::get($elemento->fichero));
                $elemento->fichero = 'gestor/Empresa/'.$elemento->cif.'.pdf';
                $elemento->save();
                $canvis++;
            }
            else {
                if (file_exists(storage_path('app/gestor/Empresa/' . $elemento->cif.'.pdf'))) {
                    $elemento->fichero = 'gestor/Empresa/' . $elemento->cif.'.pdf';
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
