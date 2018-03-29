<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterExpedientesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('expedientes',function (Blueprint $table){
            $table->string('idModulo',6)->nullable();
            $table->foreign('idModulo')->references('codigo')->on('modulos')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('expedientes',function (Blueprint $table){
            $table->dropColumn('idModulo');
            $table->dropForeign('expedientes_idModulo_foreign');
        });
    }
}
