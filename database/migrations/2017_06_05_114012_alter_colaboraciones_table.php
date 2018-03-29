<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterColaboracionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('colaboraciones',function (Blueprint $table){
            $table->dropColumn('idEmpresa');
            $table->Integer('idCentro')->references('id')->on('centros');
            $table->string('instructor',100)->nullable();
            $table->string('dni',10)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('colaboraciones',function (Blueprint $table){
            $table->Integer('idEmpresa')->references('id')->on('empresas');
            $table->dropColumn('idCentro');
            $table->dropColumn('instructor');
            $table->dropColumn('dni');
        });
    }
}
