<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAlumnosTable extends Migration
{
    public function up()
    {
        Schema::create('alumnos', function (Blueprint $table) {
            $table->string('nia',8)->primary();
            $table->string('dni',10)->unique();// arreglado
            $table->string('nombre',25);
            $table->string('apellido1',25);
            $table->string('apellido2',25);
            $table->string('password',100);
            $table->string('email',45);
            $table->string('expediente',10)->nullable();
            $table->string('domicilio',90);
            $table->string('provincia',2)->nullable();
            $table->string('municipio',5)->nullable();
            $table->string('telef1',14);
            $table->string('telef2',14)->nullable();
            $table->string('sexo',1);
            $table->date('fecha_nac');
            $table->string('codigo_postal',5)->nullable();
            $table->date('fecha_ingreso')->nullable();
            $table->date('fecha_matricula');
            $table->string('foto',60)->nullable();
            $table->tinyInteger('repite')->unsigned();
            $table->string('turno',1);
            $table->string('trabaja',1)->nullable();
            $table->integer('rol')->default(5);
            $table->rememberToken();
            
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
        Schema::drop('alumnos');
    }
}
