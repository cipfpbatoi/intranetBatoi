<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/** Afig l'accés al panell de convalidacions de l'alumnat. */
return new class extends Migration
{
    /** Crea l'entrada si encara no existix. */
    public function up(): void
    {
        if (DB::table('menus')->where('nombre', 'convalidar')->where('rol', config('roles.rol.alumno'))->exists()) {
            return;
        }

        DB::table('menus')->insert([
            'nombre' => 'convalidar',
            'url' => '/alumno/convalidacions',
            'class' => 'fa-exchange',
            'rol' => config('roles.rol.alumno'),
            'menu' => 'general',
            'submenu' => 'alumno',
            'activo' => 1,
            'orden' => 7,
            'ajuda' => '',
        ]);
    }

    /** Elimina només l'entrada creada per esta funcionalitat. */
    public function down(): void
    {
        DB::table('menus')->where('nombre', 'convalidar')->where('rol', config('roles.rol.alumno'))->delete();
    }
};
