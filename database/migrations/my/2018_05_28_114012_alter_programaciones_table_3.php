<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterProgramacionesTable3 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('programaciones', function (Blueprint $table) {
            $table->foreign('idModuloCiclo')->references('id')->on('modulo_ciclos')->onUpdate('cascade');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       Schema::table('programaciones',function (Blueprint $table){
            $table->dropForeign('programaciones_idModuloCiclo_foreign');
            
        }); 
    }
}
