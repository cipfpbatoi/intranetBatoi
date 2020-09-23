<?php

namespace Intranet\Http\Controllers\API;

use Intranet\Entities\AlumnoGrupo;
use Intranet\Entities\Grupo;
use Illuminate\Http\Request;
use Intranet\Entities\Modulo_grupo;


class AlumnoGrupoController extends ApiBaseController
{

    protected $model = 'AlumnoGrupo';

    private function alumnos($misgrupos)
    {
       foreach ($misgrupos as $migrupo){
           if (isset($migrupo->idGrupo)) {
               $alumnos = AlumnoGrupo::where('idGrupo', '=',$migrupo->idGrupo)->get();
               foreach ($alumnos as $alumno) {
                   $arrayAlumnos[$alumno->idAlumno] = $alumno->Alumno->nameFull;
               }
           }
       }
       asort($arrayAlumnos);
       foreach ($arrayAlumnos as $id => $name){
           $nalum['id']= $id;
           $nalum['name'] = $name;
           $misAlumnos[] = $nalum;
       }
       return $misAlumnos;
    }

    public function show($cadena,$send=true)
    {
            if (strlen($cadena)==8){
                return $this->sendResponse(AlumnoGrupo::where('idAlumno',$cadena)->first(),'OK');
            } else {
                $migrupo = Grupo::Qtutor($cadena)->get();
                return $this->alumnos($migrupo);
            }
    }



    
    public function getModulo($dni,$modulo){
        //$migrupo = Grupo::miGrupoModulo($dni,$modulo)->get();
        $misgrupos = Modulo_grupo::misModulos($dni,$modulo);
        return $this->alumnos($misgrupos);
    }

}
