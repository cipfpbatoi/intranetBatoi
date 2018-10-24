<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AlterColaboracionesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('colaboraciones', function(Blueprint $table)
		{
			$table->dropColumn('instructor');
                        $table->dropColumn('dni');
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
			$table->string('instructor', 300)->nullable();
			$table->string('dni', 30)->nullable();
		});
	}

}
