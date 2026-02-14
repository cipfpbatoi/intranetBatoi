<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Intranet\Entities\Profesor;


class DefaultUser extends Seeder
{

    public function run()
    {
        Profesor::create([
            'dni' => '099999999Z',
            'codigo' => '9999',
            'nombre' => 'Admin',
            'apellido1' => 'Administrador',
            'apellido2' => '',
            'password' => bcrypt('12345678'),
            'emailItaca' => 'admin@intranet.my',
            'email' => 'admin@intranet.my',
            'domicilio' => '',
            'movil1' => '',
            'movil2' => '',
            'sexo' => '',
            'codigo_postal' => '',
            'departamento' => 1,
            'fecha_ingreso' => null,
            'fecha_nac' => null,
            'fecha_baja' => null,
            'fecha_ant' => null,
            'sustituye_a' => null,
            'foto' => null,
            'rol' => '66',
            'remember_token' => null,
            'created_at' => null,
            'updated_at' => null,
            'last_logged' => null,
            'activo' => 1,
            'idioma' => 'ca',
            'api_token' => '',
            'mostrar' => 0,
        ]);
    }

}
