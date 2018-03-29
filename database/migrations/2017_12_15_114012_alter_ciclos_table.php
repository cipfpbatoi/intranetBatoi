<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCiclosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ciclos',function (Blueprint $table){
            $table->tinyInteger('tipo')->unsigned();
            $table->string('normativa',10)->default('LOE');
        });
        Schema::table('grupos',function (Blueprint $table){
            $table->tinyInteger('curso')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ciclos',function (Blueprint $table){
            $table->dropColumn('tipo');
            $table->dropColumn('normativa');
        });
        Schema::table('grupos',function (Blueprint $table){
            $table->dropColumn('curso');
        });
    }
}
