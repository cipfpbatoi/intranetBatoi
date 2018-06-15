<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterReservasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reservas',function (Blueprint $table){
            $table->string('idEspacio',10)->references('aula')->on('espacios');
            $table->dropColumn('idRecurso');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reservas',function (Blueprint $table){
            $table->dropColumn('idEspacio');
            $table->Integer('idRecurso')->references('id')->on('recursos');
        });
    }
}
