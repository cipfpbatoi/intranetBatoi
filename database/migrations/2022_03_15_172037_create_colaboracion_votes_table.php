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
			$table->integer('option_id')->unsigned()->index('colaboracion_votes_option_id_foreign');
			$table->integer('idColaboracion')->unsigned()->index('colaboracion_votes_idcolaboracion_foreign');
			$table->boolean('value')->nullable();
			$table->text('text', 65535)->nullable();
			$table->string('curs')->nullable();
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
