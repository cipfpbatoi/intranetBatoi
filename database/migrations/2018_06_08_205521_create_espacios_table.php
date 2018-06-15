<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateEspaciosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('espacios', function(Blueprint $table)
		{
			$table->string('aula', 10)->primary();
			$table->string('descripcion', 50);
			$table->boolean('idDepartamento')->index('espacios_departamento_foreign');
			$table->string('gMati', 5)->nullable();
			$table->string('gVesprada', 5)->nullable();
			$table->boolean('reservable')->default(0);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('espacios');
	}

}
