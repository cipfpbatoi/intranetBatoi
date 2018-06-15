<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToInstructorFctsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('instructor_fcts', function(Blueprint $table)
		{
			$table->foreign('idFct')->references('id')->on('fcts')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('idInstructor')->references('dni')->on('instructores')->onUpdate('RESTRICT')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('instructor_fcts', function(Blueprint $table)
		{
			$table->dropForeign('instructor_fcts_idfct_foreign');
			$table->dropForeign('instructor_fcts_idinstructor_foreign');
		});
	}

}
