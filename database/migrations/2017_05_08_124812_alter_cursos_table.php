<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCursosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cursos',function (Blueprint $table){
            $table->time('hora_ini');
            $table->time('hora_fin');
            $table->smallInteger('aforo')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cursos',function (Blueprint $table){
            $table->dropColumn('aforo');
            $table->dropColumn('hora_ini');
            $table->dropColumn('hora_fin');
        });
    }
}
