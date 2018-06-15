<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCentrosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('centros',function (Blueprint $table){
            $table->string('nombre',100);
            $table->string('horarios',100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('colaboraciones',function (Blueprint $table){
            $table->dropColumn('nombre');
            $table->dropColumn('horarios');
        });
    }
}
