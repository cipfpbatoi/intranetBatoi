<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateColaboracionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('colaboraciones', function (Blueprint $table) {
            $table->increments('id');
            $table->Integer('idEmpresa');
            $table->Integer('idCiclo');
            $table->string('contacto',50)->nullable();
            $table->string('tutor',50)->nullable();
            $table->string('telefono',10)->nullable();
            $table->tinyInteger('puestos')->default(1);
            
        });
    }
   
            

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('colaboraciones');
    }
}
