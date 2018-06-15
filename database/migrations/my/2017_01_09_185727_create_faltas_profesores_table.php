<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFaltasProfesoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('faltas_profesores', function (Blueprint $table) {
            $table->increments('id');
            $table->string('idProfesor',10);
            $table->date('dia');
            $table->time('entrada')->nullable();
            $table->time('salida')->nullable();
            
            
            $table->foreign('idProfesor')->references('dni')->on('profesores')->onDelete('cascade');
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
        Schema::dropIfExists('faltas_profesores');
    }
}
