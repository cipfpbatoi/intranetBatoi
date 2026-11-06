<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Carrega el catàleg mínim per a una instal·lació nova.
 */
class NewProject extends Seeder
{
    /**
     * Importa les dades inicials sense perdre els menús incorporats per migracions recents.
     */
    public function run(): void
    {
        if (DB::table('menus')->where('url', '/perfil')->exists()) {
            return;
        }

        $sql = file_get_contents(public_path('initial.sql'));

        DB::transaction(function () use ($sql): void {
            // Les migracions més recents creen menús amb IDs automàtics abans del seed.
            // Es reubiquen per a conservar-los i deixar lliures els IDs del catàleg base.
            DB::table('menus')->update(['id' => DB::raw('id + 1000')]);
            // La migració d'avisos crea un ajust abans del catàleg i també usa ID automàtic.
            DB::table('settings')->update(['id' => DB::raw('id + 1000')]);
            DB::unprepared($sql);
        });
    }
}
