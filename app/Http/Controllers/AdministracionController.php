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
use Intranet\Entities\Ciclo;
use DB;
use Intranet\Entities\Horario;
use Intranet\Entities\Grupo;
use Styde\Html\Facades\Alert;
use Intranet\Entities\Profesor;

class AdministracionController extends Controller{
    
    
    public function simplifica(){
        if (Session::get('completa')) Session::forget('completa');
        else Session::put('completa',1);    
        return back();
    }
    
    public function lang($lang)
    {
        Session::put('lang', $lang);
        return back();
    }
    
    protected function deleteProgramacionIndex(){
        $cuantas = Programacion::where('estado',3)->where('curso','!=',Curso())->count();
        return view('programacion.deleteOld',compact('cuantas'));
    }
    protected function deleteProgramacion(){
        Programacion::where('estado',4)->delete();
        Programacion::where('curso','!=',Curso())->update(['estado' => 4]);
        return back();
    }
    
    public function allApiToken()
    {
        $remitente = ['nombre' => 'Intranet', 'email' => 'intranet@cipfpbatoi.es'];
        foreach (Profesor::Activo()->get() as $profesor) {
            dispatch(new SendEmail($profesor->email, $remitente, 'email.apitoken', $profesor));
        }
        Alert::info('Correus enviats');
        return back();
    }
    
    protected function nuevoCursoIndex(){
        return view('nuevo.curso');
    }
    protected function nuevoCurso(){
        //$this->checkForeignKeys(false);
        $tables = ['actividades','comisiones','cursos','expedientes','faltas','faltas_itaca','faltas_profesores',
            'fcts','grupos_trabajo','guardias','horarios','incidencias','notifications','ordenes_trabajo','reservas',
            'resultados','reuniones','tutorias_grupos','activities'];
        foreach ($tables as $tabla) {
                DB::table($tabla)->delete();
        } 
        
        //$this->checkForeignKeys(true);
        return back();
    }
}

