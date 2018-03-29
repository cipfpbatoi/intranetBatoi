<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComisionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comisiones', function (Blueprint $table) {
            $table->increments('id');
            $table->string('idProfesor',10);
            $table->text('servicio');
            $table->decimal('alojamiento',5,2);
            $table->decimal('comida',5,2);
            $table->decimal('gastos',5,2);
            $table->string('medio',30);
            $table->string('marca',30);
            $table->string('matricula',10);
            $table->string('otros',80);
            $table->Integer('kilometraje')->unsigned();
            $table->dateTime('salida');
            $table->dateTime('entrada');
            $table->tinyInteger('estado')->default(0);
            $table->boolean('fct')->default(true);
            
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
        Schema::drop('comisiones');
    }
}
