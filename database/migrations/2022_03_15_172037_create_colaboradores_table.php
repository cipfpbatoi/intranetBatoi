<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateColaboradoresTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('colaboradores', function(Blueprint $table)
		{
			$table->integer('idFct')->unsigned();
			$table->string('idInstructor', 10)->index('instructor_fcts_idinstructor_foreign');
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
		Schema::drop('colaboradores');
	}

}
