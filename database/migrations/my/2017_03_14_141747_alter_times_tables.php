<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTimesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('comisiones',function (Blueprint $table){
            $table->renameColumn('salida','desde');
            $table->renameColumn('entrada','hasta');
        });
        Schema::table('actividades',function (Blueprint $table){
            $table->renameColumn('salida','desde');
            $table->renameColumn('entrada','hasta');
        });
        Schema::table('faltas',function (Blueprint $table){
            $table->renameColumn('desdeDia','desde');
            $table->renameColumn('hastaDia','hasta');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
