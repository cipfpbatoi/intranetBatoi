<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateVotesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('votes', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('user_id', 250)->index();
			$table->integer('option_id')->unsigned()->index('votes_option_id_foreign');
			$table->integer('idOption1')->unsigned()->index('votes_idmodulogrupo_foreign');
			$table->string('idOption2', 10)->nullable()->index('votes_idprofesor_index');
			$table->boolean('value')->nullable();
			$table->text('text', 65535)->nullable();
			$table->timestamps();
			$table->integer('idPoll')->unsigned()->index('votes_idpoll_foreign');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('votes');
	}

}
