<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterFctsTable3 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fcts',function (Blueprint $table){
            $table->foreign('idInstructor')->references('dni')->on('instructores')
                    ->onUpdate('cascade')->onDelete('restrict');
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
            $table->dropForeign('fcts_idInstructor_foreign');
        });
    }
}
