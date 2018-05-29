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
//    protected function deleteProgramacion(){
//        Programacion::where('hasta','<=',Hoy())->delete();
//        return back();
//    }
    protected function deleteProgramacion(){
        $todas = Programacion::all();
        foreach ($todas as $prg){
            $horarios = Horario::select('modulo','idGrupo')
                ->Profesor($prg->idProfesor)
                ->where('modulo', $prg->idModulo)
                ->distinct() 
                ->get();
            if ($horarios->count() == 1){
                $horario = $horarios->first();
                $grupo = Grupo::find($horario->idGrupo);
                $mc = Modulo_ciclo::where('idModulo',$horario->modulo)
                        ->where('idCiclo',$grupo->idCiclo)
                        ->first();
                $prg->idModuloCiclo = $mc->id;
                $prg->curso = Curso();
                $prg->save();
                }    
            else{
                $find = false;
                if ($horarios->count() == 2){
                   $ciclo1 = Grupo::find($horarios[0]->idGrupo)->idCiclo; 
                   $ciclo2 = Grupo::find($horarios[1]->idGrupo)->idCiclo; 
                   if ($ciclo1 == $ciclo2 && $horarios[0]->modulo == $horarios[1]->modulo){
                      $mc = Modulo_ciclo::where('idModulo',$horarios[0]->modulo)
                        ->where('idCiclo',$ciclo1)
                        ->first();
                        $prg->idModuloCiclo = $mc->id;
                        $prg->curso = Curso();
                        $prg->save(); 
                        $find = true;
                   }
                   
                }
                if (!$find){
                    foreach ($horarios as $horario){
                        $find = Ciclo::where('ciclo',$prg->ciclo)->first();
                        if ($find){
                           $mc = Modulo_ciclo::where('idModulo',$horario->modulo)
                            ->where('idCiclo',$find->id)
                            ->first(); 
                           $prg->idModuloCiclo = $mc->id;
                           $prg->curso = Curso();
                           $prg->save();
                        }
                        else {
                            $prg->idModuloCiclo = null;
                            $prg->curso = Curso();
                            $prg->save();
                            Alert::danger("Programació $prg->id no troba horari $prg->Modulo->literal $prg->ciclo");
                        }
                    }       
                }
            }
        }
                
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

