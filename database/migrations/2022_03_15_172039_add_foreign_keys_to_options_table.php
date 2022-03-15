<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToOptionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('options', function(Blueprint $table)
		{
			$table->foreign('ppoll_id')->references('id')->on('ppolls')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('options', function(Blueprint $table)
		{
			$table->dropForeign('options_ppoll_id_foreign');
		});
	}

}
