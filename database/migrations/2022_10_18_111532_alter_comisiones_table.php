<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterComisionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('comisiones',function (Blueprint $table){
           $table->dropColumn('medio');
           $table->renameColumn('otros','medio');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('comisiones',function (Blueprint $table){
            $table->renameColumn('medio','otros');
            $table->string('medio',30)->nullable();
        });
    }
}
