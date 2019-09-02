<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePpollsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ppolls', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->tinyInteger('who')->default(1);
            $table->tinyInteger('what')->dafault(1);
            $table->tinyInteger('anonymous')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('ppolls');
    }
}
