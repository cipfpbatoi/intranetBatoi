<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdenTrabajo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create('ordenes_trabajo', function (Blueprint $table) {
            $table->increments('id',11);
            $table->string('descripcion', 255);
            $table->tinyInteger('estado')->default(0);
            $table->string('idProfesor', 10)->nullable();
            $table->tinyInteger('tipo')->default(0);
            $table->timestamps();
            $table->foreign('idProfesor')->references('dni')->on('profesores')->onUpdate('cascade')->onDelete('SET NULL');
     
        });
       
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ordenes_trabajo',function (Blueprint $table){
            $table->dropForeign('ordenes_trabajo_idprofesor_foreign');
        });
       Schema::drop('ordenes_trabajo');
    }
}
