<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToPollsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('polls', function(Blueprint $table)
		{
			$table->foreign('idPPoll')->references('id')->on('ppolls')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('polls', function(Blueprint $table)
		{
			$table->dropForeign('polls_idppoll_foreign');
		});
	}

}
