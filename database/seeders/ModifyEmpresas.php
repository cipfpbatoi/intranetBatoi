<?php
namespace Database\Seeder;

use Intranet\Entities\Empresa;
use Intranet\Entities\Centro;
use Intranet\Entities\Colaboracion;
use Illuminate\Database\Seeder;


class ModifyEmpresas extends Seeder
{
    public function run()
    {
        $colaboraciones = Colaboracion::all();
        foreach ($colaboraciones as $colaboracion){
            $centro = Centro::find($colaboracion->idCentro);
            if ($centro)
            {
                $colaboracion->email = $colaboracion->email == ''?$centro->email:$colaboracion->email;
                $colaboracion->instructor = $colaboracion->instructor == ''?$centro->instructor:$colaboracion->instructor;
                $colaboracion->dni = $colaboracion->dni == ''?$centro->dni:$colaboracion->dni;
                $colaboracion->telefono = $colaboracion->telefono == ''?$centro->telefono:$colaboracion->telefono;
                $colaboracion->contacto = $colaboracion->contacto == ''?$centro->instructor:$colaboracion->contacto;
                $colaboracion->save();
            }
        }
        $centros = Centro::all();
        foreach ($centros as $centro){
           $empresa = Empresa::find($centro->idEmpresa);
           if ($empresa)
           {
               $centro->nombre = $centro->nombre == ''?$empresa->nombre:$centro->nombre;
               $centro->horarios = ($centro->horarios == NULL||$centro->horarios == '')?$empresa->horarios:$centro->horarios;
               $centro->save();
           }
        }
    }

}
