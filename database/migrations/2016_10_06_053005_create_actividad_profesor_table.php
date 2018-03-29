<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActividadProfesorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actividad_profesor', function (Blueprint $table) {
            $table->unsignedInteger('idActividad');
            $table->string('idProfesor',10);
            $table->boolean('coordinador')->default('0');
            
            $table->primary(['idActividad','idProfesor']);
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('actividad_profesor');//
    }
}
