<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCiclosTable2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ciclos',function (Blueprint $table){
            $table->string('vliteral',100)->nullable();
            $table->string('cliteral',100)->nullable();
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
            $table->dropColumn('vliteral');
            $table->dropColumn('cliteral');
        });
    }
}
