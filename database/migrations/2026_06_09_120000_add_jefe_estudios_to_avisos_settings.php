<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Afig el càrrec de cap d'estudis a la configuració persistida d'avisos.
 */
class AddJefeEstudiosToAvisosSettings extends Migration
{
    /**
     * Crea o actualitza l'entrada que abans depenia de la configuració estàtica.
     *
     * @return void
     */
    public function up()
    {
        DB::table('settings')->updateOrInsert(
            ['collection' => 'avisos', 'key' => 'jefeEstudios'],
            ['value' => '021657327K']
        );
    }

    /**
     * Elimina l'entrada creada per aquesta migració.
     *
     * @return void
     */
    public function down()
    {
        DB::table('settings')
            ->where('collection', 'avisos')
            ->where('key', 'jefeEstudios')
            ->where('value', '021657327K')
            ->delete();
    }
}
