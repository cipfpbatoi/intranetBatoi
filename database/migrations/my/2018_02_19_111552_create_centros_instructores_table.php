<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCentrosInstructoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('centros_instructores', function (Blueprint $table) {
            $table->unsignedInteger('idCentro');
            $table->string('idInstructor',10);
            $table->primary(['idCentro','idInstructor']);
            $table->foreign('idCentro')->references('id')->on('centros')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('idInstructor')->references('dni')->on('instructores')->onUpdate('cascade')->onDelete('cascade');
        });
    }
   
            

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('centros_instructores');
    }
}
