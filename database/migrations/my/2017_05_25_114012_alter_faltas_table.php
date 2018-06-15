<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterFaltasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('faltas',function (Blueprint $table){
            $table->time('hora_ini')->nullable();
            $table->time('hora_fin')->nullable();
            $table->boolean('dia_completo')->nullable();
            $table->date('desde')->change();
            $table->date('hasta')->nullable()->change();
            $table->dropColumn('fecha');
            $table->boolean('baja')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('faltas',function (Blueprint $table){
            $table->dropColumn('hora_ini');
            $table->dropColumn('hora_fin');
            $table->dropColumn('dia_completo');
            $table->dropColumn('baja');
        });
    }
}
