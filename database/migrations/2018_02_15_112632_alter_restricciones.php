<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterRestricciones extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
         Schema::table('fcts', function (Blueprint $table) {
            //$table->dropForeign('fcts_idcolaboracion_foreign');
            $table->foreign('idColaboracion')->references('id')->on('colaboraciones')->onUpdate('cascade')->onDelete('cascade');
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
            $table->dropForeign('fcts_idColaboracion_foreign');
        });
    }
}
