<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AlterColaboracionesTable1 extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('colaboraciones', function(Blueprint $table)
		{
			$table->tinyInteger('estado')->default(0);
                });
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('colaboraciones', function(Blueprint $table)
		{
			$table->dropColumn('estado');
		});
	}

}
