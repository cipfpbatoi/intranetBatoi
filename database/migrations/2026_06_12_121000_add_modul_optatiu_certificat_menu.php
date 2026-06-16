<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Afig l'entrada de menú del panell de certificats de mòduls optatius.
 */
return new class extends Migration
{
    /**
     * Crea l'entrada de menú si no existeix.
     */
    public function up(): void
    {
        $exists = DB::table('menus')
            ->where('nombre', 'certOptatiu')
            ->exists();

        if ($exists) {
            return;
        }

        DB::table('menus')->insert([
            'nombre' => 'certOptatiu',
            'url' => '/modul-optatiu-certificat',
            'class' => 'fa-file-pdf-o',
            'rol' => config('roles.rol.profesor'),
            'menu' => 'docencia',
            'submenu' => '',
            'activo' => 1,
            'orden' => 9999,
            'ajuda' => '',
        ]);
    }

    /**
     * Retira l'entrada de menú creada.
     */
    public function down(): void
    {
        DB::table('menus')
            ->where('nombre', 'certOptatiu')
            ->delete();
    }
};
