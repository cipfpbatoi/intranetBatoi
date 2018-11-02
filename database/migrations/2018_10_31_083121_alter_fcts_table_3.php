<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AlterFctsTable3 extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('fcts', function(Blueprint $table)
		{
                    $table->dropColumn('correoAlumno');
                });
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('fcts', function(Blueprint $table)
		{
			$table->boolean('correoAlumno')->default(0);
                });
	}

}
