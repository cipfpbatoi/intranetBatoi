<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToVotesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('votes', function(Blueprint $table)
		{
			$table->foreign('idPoll')->references('id')->on('polls')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('option_id')->references('id')->on('options')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('votes', function(Blueprint $table)
		{
			$table->dropForeign('votes_idpoll_foreign');
			$table->dropForeign('votes_option_id_foreign');
		});
	}

}
