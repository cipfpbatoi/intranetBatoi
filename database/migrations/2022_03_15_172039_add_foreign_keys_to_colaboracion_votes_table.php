<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToColaboracionVotesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('colaboracion_votes', function(Blueprint $table)
		{
			$table->foreign('idColaboracion')->references('id')->on('colaboraciones')->onUpdate('NO ACTION')->onDelete('NO ACTION');
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
		Schema::table('colaboracion_votes', function(Blueprint $table)
		{
			$table->dropForeign('colaboracion_votes_idcolaboracion_foreign');
			$table->dropForeign('colaboracion_votes_option_id_foreign');
		});
	}

}
