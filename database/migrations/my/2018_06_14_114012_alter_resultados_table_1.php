<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterResultadosTable1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('resultados',function (Blueprint $table){
            $table->Integer('idModuloGrupo')->unsigned()->nullable();
            $table->unique(['idModuloGrupo','evaluacion']);
            $table->foreign('idModuloGrupo')->references('id')->on('modulo_grupos')
                    ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('resultados',function (Blueprint $table){
            $table->dropForeign('resultados_idmodulogrupo_foreign');
            $table->dropColumn('idModuloGrupo');
            
        });
    }
}
