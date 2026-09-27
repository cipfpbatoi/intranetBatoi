<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Intranet\Application\Menu\MenuService;

/** Retira l'accés principal perquè el gestor XML viu dins de convalidacions de Direcció. */
return new class extends Migration
{
    /** Elimina l'entrada principal creada inicialment. */
    public function up(): void
    {
        DB::table('menus')->where('url', '/convalidacions/xml')->delete();
        app(MenuService::class)->clearCache('general');
    }

    /** Restaura l'entrada només si es desfà esta correcció. */
    public function down(): void
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

        app(MenuService::class)->clearCache('general');
    }
};
