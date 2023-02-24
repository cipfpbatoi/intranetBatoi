<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInstructoresTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('instructores', function (Blueprint $table)
		{
			$table->string('dni', 12)->primary();
			$table->string('name', 60);
			$table->string('email', 150)->nullable();
			$table->string('telefono', 20)->nullable();
			$table->string('departamento', 80)->nullable();
			$table->string('surnames', 60)->default('');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('instructores');
	}

}
