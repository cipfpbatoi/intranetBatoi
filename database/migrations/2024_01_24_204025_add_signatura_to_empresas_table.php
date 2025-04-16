<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSignaturaToEmpresasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('empresas', function (Blueprint $table) {
            // Afegeix un nou camp de tipus string anomenat 'flexible'
            // Pots canviar 'string' per un altre tipus de dada si ho necessites
            $table->date('data_signatura')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('empresas', function (Blueprint $table) {
            // Si vols fer un "rollback", aquesta instrucció eliminarà el camp 'flexible'
            $table->dropColumn('data_signatura');

        });
    }
}
