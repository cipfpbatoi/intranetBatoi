<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AlterAlldepartamentosTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('espacios',function (Blueprint $table){
            $table->renameColumn('departamento','idDepartamento');
            $table->foreign('idDepartamento')->references('id')->on('departamentos')->onUpdate('cascade');
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('espacios',function (Blueprint $table){
            $table->dropForeign('espacios_idDepartamento_foreign');
            $table->renameColumn('idDepartamento','departamento');
        });
    }
}
