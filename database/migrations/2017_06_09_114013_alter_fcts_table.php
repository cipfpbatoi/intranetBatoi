<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterFctsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fcts',function (Blueprint $table){
            //$table->dropColumn('idEmpresa');
            $table->smallInteger('horas_semanales')->default(40);
            $table->date('hasta')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fcts',function (Blueprint $table){
            $table->dropColumn('horas_semanales');
        });
    }
}
