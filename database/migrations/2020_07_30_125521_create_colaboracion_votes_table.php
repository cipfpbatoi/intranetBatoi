<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateColaboracionVotesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('colaboracion_votes', function(Blueprint $table)
		{
            $table->increments('id');
            $table->integer('option_id')->unsigned();
            $table->integer('idColaboracion')->unsigned();
            $table->tinyInteger('value')->nullable();
            $table->text('text')->nullable();
            $table->string('curs')->nullable();

            $table->foreign('option_id')->references('id')->on('options');
            $table->foreign('idColaboracion')->references('id')->on('colaboraciones');

        });
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('colaboracion_votes');
	}

}
