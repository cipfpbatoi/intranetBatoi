<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterModulosTable1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modulos',function (Blueprint $table){
            $table->tinyInteger('departamento')->default('99');
            $table->foreign('departamento')->references('id')->on('departamentos')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('modulos',function (Blueprint $table){
            $table->dropColumn('departamento');
        });
    }
}
