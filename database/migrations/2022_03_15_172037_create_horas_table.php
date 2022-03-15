<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateHorasTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('horas', function(Blueprint $table)
		{
			$table->boolean('codigo')->primary();
			$table->string('turno', 8);
			$table->string('hora_ini', 5);
			$table->string('hora_fin', 5);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('horas');
	}

}
