<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterResultadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('resultados',function (Blueprint $table){
            $table->tinyInteger('udProg')->nullable();
            $table->tinyInteger('udImp')->nullable();
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
            $table->dropColumn('udProg');
            $table->dropColumn('udImp');
        });
    }
}
