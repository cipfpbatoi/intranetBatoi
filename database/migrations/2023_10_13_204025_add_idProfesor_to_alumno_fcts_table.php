<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdProfesorToAlumnoFctsTable extends Migration
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
            $table->string('idProfesor',10)->collation('utf8mb3_unicode_ci')->nullable()->index();
            $table->foreign('idProfesor')->references('dni')->on('profesores')
                ->onDelete('set null')
                ->onUpdate('cascade');
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
            // Si vols fer un "rollback", aquesta instrucció eliminarà el camp 'flexible'
            $table->dropColumn('idProfesor');
            $table->dropForeign(['idProfesor']);
        });
    }
}
