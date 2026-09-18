<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/** Afig l'accés al panell de convalidacions de Direcció. */
return new class extends Migration
{
    /** Crea l'entrada dins del submenú de Direcció si no existix. */
    public function up(): void
    {
        if (DB::table('menus')->where('nombre', 'convalidar')->where('rol', config('roles.rol.direccion'))->exists()) {
            return;
        }

        DB::table('menus')->insert([
            'nombre' => 'convalidar',
            'url' => '/direccion/convalidacions',
            'class' => 'fa-exchange',
            'rol' => config('roles.rol.direccion'),
            'menu' => 'general',
            'submenu' => 'direccion',
            'activo' => 1,
            'orden' => 8,
            'ajuda' => '',
        ]);
    }

    /** Elimina només l'entrada de Direcció creada per esta migració. */
    public function down(): void
    {
        DB::table('menus')
            ->where('nombre', 'convalidar')
            ->where('rol', config('roles.rol.direccion'))
            ->delete();
    }
};
