<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Intranet\Entities\Profesor;


class DefaultUser extends Seeder
{

    public function run()
    {
        Profesor::create([
            'dni' => '00000015S',
            'codigo' => '9999',
            'nombre' => 'Usuari',
            'apellido1' => 'De prova',
            'apellido2' => '',
            'password' => bcrypt('Prova2026!'),
            'emailItaca' => 'admin.prova@exemple.invalid',
            'email' => 'admin.prova@exemple.invalid',
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
            'changePassword' => now(),
            'activo' => 1,
            'idioma' => 'ca',
            'api_token' => '',
            'mostrar' => 0,
        ]);
    }

}
