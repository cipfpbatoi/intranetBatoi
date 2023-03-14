<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterFctTable3 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fcts', function (Blueprint $table)
        {
           $table->string('cotutor', 10)->nullable();
           $table->foreign('cotutor')->references('dni')->on('profesores')->onDelete('restrict')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fcts', function (Blueprint $table)
        {
            $table->dropColumn('fcts');
        });
    }
}
