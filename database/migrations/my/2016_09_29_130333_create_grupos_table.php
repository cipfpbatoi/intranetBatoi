<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGruposTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create('grupos', function (Blueprint $table) {
            $table->string('codigo',5)->primary();
            $table->string('nombre',45);
            $table->string('turno',1);
            $table->string('tutor',10)->nullable();
            $table->tinyInteger('idCiclo')->nullable();
            
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
        Schema::drop('grupos');//
    }
}
