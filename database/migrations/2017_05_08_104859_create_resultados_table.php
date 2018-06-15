<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResultadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('resultados', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('evaluacion');
            $table->tinyInteger('matriculados');
            $table->tinyInteger('evaluados');
            $table->tinyInteger('aprobados');
            $table->text('observaciones')->nullable();
            $table->string('idProfesor',10);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('resultados');
    }
}
