<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSolicitudesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('solicitudes', function (Blueprint $table) {
            $table->id();
            $table->string('idAlumno', 8)->collation('utf8_unicode_ci')->index('solicitudes_idalumno_foreign');
            $table->string('idProfesor', 10)->collation('utf8_unicode_ci')->index('solicitudes_idprofesor_foreign');
            $table->text('text1', 65535);
            $table->text('text2', 65535)->nullable();
            $table->text('text3', 65535)->nullable();
            $table->date('fecha');
            $table->date('fechasolucion')->nullable();
            $table->boolean('estado')->default(0);
            $table->string('idOrientador', 10)->collation('utf8_unicode_ci')->nullable()->index('solicitudes_idorientador_foreign');;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('solicitudes');
    }
}
