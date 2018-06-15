<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInstructorFctsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('instructor_fcts', function(Blueprint $table)
		{
			$table->integer('idFct')->unsigned();
			$table->string('idInstructor', 100)->index('instructor_fcts_idinstructor_foreign');
			$table->smallInteger('horas')->nullable();
			$table->primary(['idFct','idInstructor']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('instructor_fcts');
	}

}
