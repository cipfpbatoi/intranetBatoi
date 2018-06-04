<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModulociclosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modulo_ciclos', function (Blueprint $table) {
//            $table->charset = 'utf8';
//            $table->collation = 'utf8_unicode_ci';
            $table->increments('id');
            $table->string('idModulo',6);
            $table->integer('idCiclo')->unsigned();
            $table->string('curso',1);
            $table->string('enlace',200)->nullable();
            $table->foreign('idCiclo')->references('id')->on('ciclos')
                    ->onUpdate('cascade')->onDelete('restrict');
            $table->index('idModulo');
            $table->foreign('idModulo')->references('codigo')->on('modulos')
                    ->onUpdate('cascade')->onDelete('cascade');
            $table->unique(['idModulo','idCiclo']);
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('modulo_ciclos');
    }
}
