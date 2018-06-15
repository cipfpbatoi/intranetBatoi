<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterIncidenciasTable2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('incidencias',function (Blueprint $table){
            $table->Integer('orden')->unsigned()->nullable();
            $table->foreign('orden')->references('id')->on('ordenes_trabajo')->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('incidencias',function (Blueprint $table){
            $table->dropColumn('orden');
            $table->dropForeign('incidencias_orden_foreign');
        });
    }
}
