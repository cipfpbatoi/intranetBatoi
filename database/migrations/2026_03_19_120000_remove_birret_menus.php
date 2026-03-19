<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Migració per retirar entrades de menú legacy de birret.
 */
return new class extends Migration
{
    /**
     * Elimina menús residuals de birret ja retirats del producte.
     */
    public function up(): void
    {
        DB::table('menus')
            ->whereIn('nombre', ['birret', 'Authbirret'])
            ->delete();
    }

    /**
     * Restaura els menús legacy de birret només si no existixen.
     */
    public function down(): void
    {
        $legacyMenus = [
            [
                'nombre' => 'birret',
                'url' => '/birret',
                'class' => '',
                'rol' => 1,
                'menu' => 'general',
                'submenu' => '',
                'activo' => 1,
                'ajuda' => '',
            ],
            [
                'nombre' => 'Authbirret',
                'url' => '/direccion/birret',
                'class' => '',
                'rol' => 5,
                'menu' => 'general',
                'submenu' => '',
                'activo' => 1,
                'ajuda' => '',
            ],
        ];

        foreach ($legacyMenus as $legacyMenu) {
            $exists = DB::table('menus')
                ->where('nombre', $legacyMenu['nombre'])
                ->exists();

            if (!$exists) {
                DB::table('menus')->insert($legacyMenu);
            }
        }
    }
};
