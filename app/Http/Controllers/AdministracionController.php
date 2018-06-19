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
        $remitente = ['nombre' => 'Intranet', 'email' => config('contacto.host.email')];
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
            'resultados','reuniones','tutorias_grupos','modulo_grupos','activities'];
        foreach ($tables as $tabla) {
                DB::table($tabla)->delete();
        } 
        
        //$this->checkForeignKeys(true);
        return back();
    }
    
    
    
    public static function exe_actualizacion($version_antigua){
        
        $this->crea_modulosCiclos();
        $resultados = Resultado::whereNull('idModuloGrupo')->get();
        foreach ($resultados as $resultado){
            if ($mc = Modulo_ciclo::where('idModulo',$resultado->idModulo)
                    ->where('idCiclo',$resultado->Grupo->idCiclo)->first())
                if ($mg = Modulo_grupo::where('idModuloCiclo',$mc->id)->where('idGrupo',$resultado->idGrupo)
                        ->first())
                if (Resultado::where('idModuloGrupo',$mg->id)->where('evaluacion',$resultado->evaluacion)->count() == 0)
                {
                    $resultado->idModuloGrupo = $mg->id;    
                    $resultado->save();
                }
        }
        Resultado::whereNull('idModuloGrupo')->delete();
    }
    private function crea_modulosCiclos()
    {
        $enlace = (Storage::exists('public/programacions.txt')) ? true : false;
        if ($enlace) {
            $fichero = explode("\n", Storage::get('public/programacions.txt'));
            $indice = Modulo_ciclo::max('id') ? Modulo_ciclo::max('id') : 0;
        }
        $horarios = Horario::distinct()->whereNotNull('idGrupo')
                        ->whereNotNull('modulo')->whereNotNull('idProfesor')
                        ->whereNotIn('modulo', config('constants.modulosNoLectivos'))->get();
        foreach ($horarios as $horario) {
            if (isset($horario->Grupo->idCiclo)) {
                if (Modulo_ciclo::where('idModulo', $horario->modulo)->where('idCiclo', $horario->Grupo->idCiclo)->count() == 0) {
                    $nuevo = new Modulo_ciclo();
                    $nuevo->idModulo = $horario->modulo;
                    $nuevo->idCiclo = $horario->Grupo->idCiclo;
                    $nuevo->curso = substr($horario->idGrupo, 0, 1);
                    $nuevo->idDepartamento = isset(Profesor::find($horario->idProfesor)->departamento)?Profesor::find($horario->idProfesor)->departamento:'';
                    if ($enlace)
                        $nuevo->enlace = $fichero[$indice++];
                    $nuevo->save();
                }
                else{
                    $nuevo =  Modulo_ciclo::where('idModulo', $horario->modulo)->where('idCiclo', $horario->Grupo->idCiclo)->first();
                    if ((isset(Profesor::find($horario->idProfesor)->departamento))&&($nuevo->idDepartamento != Profesor::find($horario->idProfesor)->departamento))
                    {
                       $nuevo->idDepartamento = Profesor::find($horario->idProfesor)->departamento;
                       $nuevo->save();
                    }
                }
                $mc = Modulo_ciclo::where('idModulo', $horario->modulo)->where('idCiclo', $horario->Grupo->idCiclo)->first();
                if (Modulo_grupo::where('idModuloCiclo', $mc->id)->where('idGrupo',$horario->idGrupo)->count()==0){
                    $nuevo = new Modulo_grupo();
                    $nuevo->idModuloCiclo = $mc->id;
                    $nuevo->idGrupo = $horario->idGrupo;
                    $nuevo->save();
                }
                if (!Programacion::where('idModuloCiclo', $mc->id)->where('curso', Curso())->first()) {
                    $prg = New Programacion();
                    $prg->idModuloCiclo = $mc->id;
                    $prg->fichero = $mc->enlace;
                    $prg->curso = Curso();
                    if ($antigua = Programacion::where('idModuloCiclo', $mc->id)->first()) {
                        $prg->criterios = $antigua->criterios;
                        $prg->metodologia = $antigua->metodologia;
                        $prg->propuestas = $antigua->propuestas;
                    }
                    $prg->save();
                }
            }
        }
    }
}

