<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyUniqueConstraintsOnAlumnoFctsTable extends Migration
{
/**
* Run the migrations.
*
* @return void
*/
    public function up()
    {
    Schema::table('alumno_fcts', function (Blueprint $table) {
    // Elimina la restricció de clau externa
    $table->dropForeign('alumno_fcts_idalumno_foreign');
    $table->dropForeign('alumno_fcts_idfct_foreign');

    // Elimina la restricció única
    $table->dropUnique('alumno_fcts_idFct_idAlumno_unique');

    // Afegeix la nova restricció única per a idFct i idSao
    $table->unique(['idFct', 'idSao']);

    // (Opcional) Restaura la restricció de clau externa si encara la necessites
    $table->foreign('idAlumno')->references('nia')->on('alumnos');
    $table->foreign('idFct')->references('id')->on('fcts');
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
            $table->dropUnique(['idFct', 'idSao']);

            // (Opcional) Restaura la restricció única antiga i la clau externa
            $table->unique(['idFct', 'idAlumno'], 'alumno_fcts_idFct_idAlumno_unique');
            $table->foreign('idAlumno', 'alumno_fcts_idalumno_foreign')->references('nia')->on('alumnos');
            $table->foreign('idFct', 'alumno_fcts_idfct_foreign')->references('id')->on('fcts');
        });
    }
}
