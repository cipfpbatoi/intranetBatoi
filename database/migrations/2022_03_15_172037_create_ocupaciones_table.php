<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOcupacionesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ocupaciones', function(Blueprint $table)
		{
			$table->string('codigo', 10)->primary();
			$table->string('nombre', 60);
			$table->string('nom', 60);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ocupaciones');
	}

}
