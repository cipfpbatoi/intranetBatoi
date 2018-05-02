<?php
/* clase : IntranetController
 * És la classe pare de tots els controladors amb el mètodes comuns a ells
 */
namespace Intranet\Http\Controllers;

use Intranet\Http\Controllers\Controller;
use Intranet\Botones\Panel;
use Illuminate\Support\Facades\Session;
use Intranet\Entities\Programacion;
use DB;

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
        $cuantas = Programacion::where('hasta','<=',Hoy())->count();
        return view('programacion.deleteOld',compact('cuantas'));
    }
    protected function deleteProgramacion(){
        Programacion::where('hasta','<=',Hoy())->delete();
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

