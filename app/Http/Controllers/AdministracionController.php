<?php

/* clase : IntranetController
 * És la classe pare de tots els controladors amb el mètodes comuns a ells
 */

namespace Intranet\Http\Controllers;

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

class AdministracionController extends Controller
{

    public function simplifica()
    {
        if (Session::get('completa'))
            Session::forget('completa');
        else
            Session::put('completa', 1);
        return back();
    }

    public function lang($lang)
    {
        Session::put('lang', $lang);
        return back();
    }

    protected function deleteProgramacionIndex()
    {
        $cuantas = Programacion::where('estado', 3)->where('curso', '!=', Curso())->count();
        return view('programacion.deleteOld', compact('cuantas'));
    }

    protected function deleteProgramacion()
    {
        Programacion::where('estado', 4)->delete();
        Programacion::where('curso', '!=', Curso())->update(['estado' => 4]);
        return back();
    }

    public function allApiToken()
    {
        $remitente = ['nombre' => 'Intranet', 'email' => config('contacto.host.email')];
        foreach (Profesor::Activo()->get() as $profesor) {
            dispatch(new SendEmail($profesor->email, $remitente, 'email.apitoken', $profesor));
        }
        Alert::info('Correus enviats');
        return back();
    }

    protected function nuevoCursoIndex()
    {
        return view('nuevo.curso');
    }

    protected function nuevoCurso()
    {
        //$this->checkForeignKeys(false);
        $tables = ['actividades', 'comisiones', 'cursos', 'expedientes', 'faltas', 'faltas_itaca', 'faltas_profesores',
            'fcts', 'grupos_trabajo', 'guardias', 'horarios', 'incidencias', 'notifications', 'ordenes_trabajo', 'reservas',
            'resultados', 'reuniones', 'tutorias_grupos', 'modulo_grupos', 'activities'];
        foreach ($tables as $tabla) {
            DB::table($tabla)->delete();
        }

        //$this->checkForeignKeys(true);
        return back();
    }

    public function help($fichero, $enlace)
    {
        return view('intranet.readme', ['elemento' => mdFind($fichero, $enlace)]);
    }

    public static function exe_actualizacion($version_antigua)
    {
        foreach (config('constants.version') as $version) {
            if ($version > $version_antigua) {
                AdministracionController::$version();
            }
        }
    }

    public static function v1_0()
    {
        Alert::info('Version 1.0');
    }

    public static function v1_1()
    {
        Alert::info('Version 1.1');
    }

    public static function v1_2()
    {
        Alert::info('Version 1.2');
    }

    public static function v1_3()
    {
        foreach (Fct::all() as $fct) {
            $existe = FCT::find($fct->id);
            if ($existe) {
                if ($existe->Colaboradores->first()){
                    $existe->idInstructor = $existe->Colaboradores->first()->dni;
                    $existe->save();
                }
                $alFct = new AlumnoFct();
                $alFct->idFct = $fct->id;
                $alFct->idAlumno = $fct->idAlumno;
                $alFct->save();
                
                $mateixaFct = FCT::where('idColaboracion', $fct->idColaboracion)
                        ->where('idAlumno', '<>', $fct->idAlumno)
                        ->where('asociacion', $fct->asociacion)
                        ->where('desde', FechaInglesa($fct->desde))
                        ->where('horas', $fct->horas)
                        ->get();
                foreach ($mateixaFct as $mateixa) {
                        $alFct = new AlumnoFct();
                        $alFct->idFct = $fct->id;
                        $alFct->idAlumno = $mateixa->idAlumno;
                        $alFct->save();
                        $mateixa->delete();
                }
            }
        }
        FctColaborador::truncate();
        
    }
    public static function v1_3_1(){
        foreach (Fct::all() as $fct) {
            $existe = FCT::find($fct->id);
            if ($existe) {
                $mateixaFct = FCT::where('idColaboracion', $fct->idColaboracion)
                        ->where('asociacion', $fct->asociacion)
                        ->where('id','!=',$fct->id)
                        ->get();
                $hores = $fct->horas;
                foreach ($mateixaFct as $mateixa) {
                    $hores = $mateixa->horas>$hores?$mateixa->horas:$hores;
                    foreach ($mateixa->Alumnos as $alFct){
                        $fct->Alumnos()->attach($alFct->nia);
                    }
                    $mateixa->delete();
                }
                $fct->horas = $hores;
                $fct->save();
            }
        }
        foreach (\Intranet\Entities\Fct1::all() as $fct1){
            $mateixaFct = FCT::where('idColaboracion', $fct1->idColaboracion)
                 ->where('asociacion', $fct1->asociacion)
                 ->get();
            foreach($mateixaFct as $fct){
                if (!$fct->hasta) {
                    $fct->hasta = $fct1->hasta;
                    $fct->save();
                }
                foreach ($fct->Alumnos as $alumno){
                        $fct->Alumnos()->updateExistingPivot($alumno->nia,['desde'=>$fct1->desde]);
                        $fct->Alumnos()->updateExistingPivot($alumno->nia,['hasta'=>$fct1->hasta]);
                        $fct->Alumnos()->updateExistingPivot($alumno->nia,['horas'=>$fct1->horas]);
                }
            }
        }
    }

}
