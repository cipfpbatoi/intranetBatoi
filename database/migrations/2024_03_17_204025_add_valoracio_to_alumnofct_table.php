<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddValoracioToAlumnofctTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('alumno_fcts', function (Blueprint $table) {
            // Afegeix un nou camp de tipus string anomenat 'flexible'
            // Pots canviar 'string' per un altre tipus de dada si ho necessites
            $table->text('valoracio')->nullable()->after('horas')->comment('Valoració del tutor de l\'empresa');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('alumno_fcts', function (Blueprint $table) {
            $table->dropColumn('valoracio');
        });
    }
}
