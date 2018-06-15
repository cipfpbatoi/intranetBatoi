<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCiclosTable1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ciclos',function (Blueprint $table){
            $table->string('titol',100)->nullable();
            $table->string('rd',100)->nullable();
            $table->string('rd2',100)->nullable();
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
            $table->dropColumn('titol');
            $table->dropColumn('rd');
            $table->dropColumn('rd2');
        });
    }
}
