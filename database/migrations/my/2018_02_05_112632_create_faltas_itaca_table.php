<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFaltasItacaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create('faltas_itaca', function (Blueprint $table) {
            $table->increments('id',11);
            $table->string('idProfesor', 10);
            $table->date('dia');
            $table->tinyInteger('sesion_orden');
            $table->tinyInteger('estado')->default(0);
            $table->boolean('enCentro')->default(0);
            $table->string('idGrupo',5);
            $table->text('justificacion')->nullable();
            $table->foreign('idProfesor')->references('dni')->on('profesores')->onUpdate('cascade')->onDelete('cascade'); 
            $table->foreign('idGrupo')->references('codigo')->on('grupos')->onUpdate('cascade')->onDelete('cascade'); 
        });
       
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
//        Schema::table('faltas_itaca',function (Blueprint $table){
//            $table->dropForeign('faltas_itaca_idprofesor_foreign');
//        });
       Schema::drop('faltas_itaca');
    }
}
