<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterFctTable1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fcts',function (Blueprint $table){
           $table->renameColumn('periodo','periode');
           $table->renameColumn('desde','fecha');
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
            $table->renameColumn('periode','periodo');
            $table->renameColumn('fecha','desde');
        });
    }
}
