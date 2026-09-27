<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/** Afig l'accés administratiu a les fonts XML de convalidacions. */
return new class extends Migration
{
    /** Crea l'entrada administrativa si encara no existix. */
    public function up(): void
    {
        if (DB::table('menus')->where('url', '/convalidacions/xml')->exists()) {
            return;
        }

        DB::table('menus')->insert([
            'nombre' => 'ResultatsXml',
            'url' => '/convalidacions/xml',
            'class' => 'fa-file-code-o',
            'rol' => config('roles.rol.administrador'),
            'menu' => 'general',
            'submenu' => '',
            'activo' => 1,
            'orden' => 9,
            'ajuda' => '',
        ]);
    }

    /** Elimina només l'entrada creada per esta funcionalitat. */
    public function down(): void
    {
        DB::table('menus')->where('url', '/convalidacions/xml')->delete();
    }
};
