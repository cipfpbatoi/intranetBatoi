<?php


use Intranet\Entities\Modulo;
use Intranet\Entities\Modulo_ciclo;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Intranet\Entities\Horario;



class CreateModulosCiclos extends Seeder
{
    public function run()
    {
//        $todos = Modulo::all();
//        foreach ($todos as $modulo){
//            if ($modulo->idCiclo){
//                $nuevo = new Modulo_ciclo();
//                $nuevo->idModulo = $modulo->codigo;
//                $nuevo->idCiclo = $modulo->idCiclo;
//                $nuevo->save();
//            }
//        }
        $horarios = Horario::ModulosActivos()->get();
        $fichero = explode("\n",Storage::get('public/programacions.txt'));
        $indice = 0;
        foreach ($horarios as $horario){
            if (Modulo_ciclo::where('idModulo',$horario->modulo)->where('idCiclo',$horario->Grupo->idCiclo)->count()==0)
            {
                $nuevo = new Modulo_ciclo();
                $nuevo->idModulo = $horario->modulo;
                $nuevo->idCiclo = $horario->Grupo->idCiclo;
                $nuevo->curso = substr($horario->idGrupo,0,1);
                $nuevo->enlace = $fichero[$indice++];
                $nuevo->save();
            }
        }
    }

}
