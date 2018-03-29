<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHorariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('horarios', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('dia_semana',['L','M','X','J','V']);
            $table->tinyInteger('sesion_orden');
            $table->time('desde');
            $table->time('hasta');
            $table->string('idProfesor',10);
            $table->string('modulo',6)->nullable();
            $table->string('idGrupo',5)->nullable();
            $table->string('aula',5)->nullable();
            $table->string('ocupacion',10)->nullable();
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
        Schema::dropIfExists('horarios');
    }
}
