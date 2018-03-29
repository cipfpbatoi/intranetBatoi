<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCentrosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('centros', function (Blueprint $table) {
            $table->increments('id');
            $table->Integer('idEmpresa');
            $table->string('direccion',100);
            $table->string('localidad',30)->nullable();
            $table->string('instructor',100)->nullable();
            $table->string('email',50)->nullable();
            $table->string('telefono',10)->nullable();
            $table->string('dni',10)->nullable();
            $table->text('observaciones')->nullable();
      
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
        Schema::drop('centros');
    }
}
