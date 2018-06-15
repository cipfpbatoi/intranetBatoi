<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdenesReunionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ordenes_reuniones', function (Blueprint $table) {
            $table->increments('id');
            $table->Integer('idReunion');
            $table->boolean('tarea')->default(0);
            $table->string('idProfesor',10)->nullable();
            $table->boolean('realizada')->default(0);
            $table->tinyInteger('orden')->default(1);
            $table->string('descripcion',120);
            $table->text('resumen')->nullable();
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ordenes_reuniones');
    }
}
