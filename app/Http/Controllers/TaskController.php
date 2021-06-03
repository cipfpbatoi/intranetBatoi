<?php

namespace Intranet\Http\Controllers;

use DB;
use ImportTableSeeder;
use Intranet\Entities\Documento;
use Intranet\Entities\Modulo_grupo;
use Intranet\Entities\Programacion;
use Intranet\Entities\Reunion;
use Intranet\Entities\Task;
use Intranet\Entities\TipoReunion;


/**
 * Class ImportController
 * @package Intranet\Http\Controllers
 */
class TaskController extends Controller
{
    private $tarea;
    const ACTA_DELEGADO = 5;
    const ACTA_AVAL = 7;
    const ACTA_FSE = 9;

    public function check($id){
        $this->tarea = Task::findOrFail($id);
        $taskTeacher = $this->tarea->myDetails;
        if ($taskTeacher){
            $this->tarea->Profesores()->detach(AuthUser()->dni);
        } else {
            $funcion = $this->tarea->action;
            if ($funcion){
                $valid = $this->$funcion();
            } else {
                $valid = 0;
            }
            $this->tarea->Profesores()->attach(AuthUser()->dni,['check'=>1,'valid'=>$valid]);
        }
       return back();
    }

    private function AvalPrg(){
        foreach (Programacion::misProgramaciones()->get() as $programacion) {
            if (is_null($programacion->propuestas) || $programacion->propuestas == '') {
                return 0;
            }
        }
        return 1;
    }

    private function EntrPrg(){
        foreach (Programacion::misProgramaciones()->get() as $programacion) {
            if ($programacion->estado == 0) {
                return 0;
            }
        }
        return 1;
    }

    private function SegAval(){
        foreach (Modulo_grupo::misModulos() as $modulo){
            if (!$modulo->resultados->where('evaluacion','<=',evaluacion())){
                return 0;
            }
        }
        return 1;
    }

    private function ActAval(){
        $howManyAre = Reunion::Convocante()->Tipo(self::ACTA_AVAL)->Archivada()->count();
        if ($howManyAre == evaluacion()) {
                    return 1;
        } else {
                    return 0;
                }

    }

    private function ActaDel(){
        return Reunion::Convocante()->Tipo(self::ACTA_DELEGADO)->Archivada()->count();
    }

    private function ActaFSE(){
        return Reunion::Convocante()->Tipo(self::ACTA_FSE)->Archivada()->count();
    }

    private function InfDept(){
        if (Documento::where('propietario', AuthUser()->FullName)->where('tipoDocumento', 'Acta')
                ->where('curso', Curso())->where('descripcion','Informe Trimestral')->count()==evaluacion()) {
            return 1;
        }
        return 0;
    }

}
