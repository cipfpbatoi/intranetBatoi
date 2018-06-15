<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class createIncidencias extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('incidencias', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('material')->nullable();
            $table->string('descripcion', 255);
            $table->tinyInteger('estado')->default(0);
            $table->string('espacio', 10)->references('aula')->on('espacios');
            $table->string('creador', 10)->references('dni')->on('profesores');
            $table->string('responsable', 10)->nullable();
            $table->tinyInteger('tipo')->references('id')->on('tipoincidencias');
            $table->tinyInteger('prioridad');
            $table->date('fecha');
            $table->string('Observaciones', 255)->nullable();
            $table->string('solucion', 255)->nullable();
            $table->date('fechasolucion')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('incidencias');
    }

}
