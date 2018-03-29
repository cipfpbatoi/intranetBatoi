<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterProgramacionesTable1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('programaciones',function (Blueprint $table){
            $table->tinyInteger('criterios')->default(0);
            $table->tinyInteger('metodologia')->default(0);
            $table->text('propuestas')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('programaciones',function (Blueprint $table){
            $table->dropColumn('criterios');
            $table->dropColumn('metodologia');
            $table->dropColumn('propuestas');
        });
    }
}
