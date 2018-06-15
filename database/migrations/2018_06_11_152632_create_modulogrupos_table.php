<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModulogruposTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modulo_grupos', function (Blueprint $table) {
            $table->increments('id');
            $table->Integer('idModuloCiclo')->unsigned();
            $table->string('idGrupo',5);
            
            $table->unique(['idModuloCiclo','idGrupo']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('modulo_grupos');
    }
}
