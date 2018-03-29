<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterProgramacionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('programaciones',function (Blueprint $table){
            //$table->dropColumn('idEmpresa');
            $table->string('ciclo',80)->nullable();
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
            $table->dropColumn('ciclo');
        });
    }
}
