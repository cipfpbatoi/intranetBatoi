<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCentrosInstructoresTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('centros_instructores', function(Blueprint $table)
		{
			$table->integer('idCentro')->unsigned();
			$table->string('idInstructor', 12)->index('centros_instructores_idinstructor_foreign');
			$table->primary(['idCentro','idInstructor']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('centros_instructores');
	}

}
