<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterInstructoresTable1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('instructores',function (Blueprint $table){
            $table->string('surnames',60)->default('');
            $table->renameColumn('nombre','name');
       });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('instructores',function (Blueprint $table){
            $table->dropColumn('surnames');
            $table->renameColumn('name','nombre');
        });
    }
}
