<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCursosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cursos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('titulo',50);
            $table->string('tipo',1)->nullable();
            $table->text('comentarios');
            $table->text('profesorado');
            $table->string('horario',6);
            $table->string('activo',1);
            $table->tinyInteger('horas')->unsigned();
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cursos');
    }
}
