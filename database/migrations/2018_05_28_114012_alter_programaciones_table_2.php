<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterProgramacionesTable2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('programaciones',function (Blueprint $table){
            $table->Integer('idModuloCiclo')->unsigned()->nullable();
            $table->string('curso',10);
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
            $table->dropColumn('idModuloCiclo');
            $table->dropColumn('curso');
        });
    }
}
