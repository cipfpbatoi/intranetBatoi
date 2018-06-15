<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateColaboracionesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('colaboraciones', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('idCiclo')->index('idCiclo');
			$table->string('contacto', 150)->nullable();
			$table->string('tutor', 150)->nullable();
			$table->string('telefono', 20)->nullable();
			$table->boolean('puestos')->nullable()->default(1);
			$table->integer('idCentro')->unsigned()->index('idCentro');
			$table->string('instructor', 300)->nullable();
			$table->string('dni', 30)->nullable();
			$table->string('email', 150)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('colaboraciones');
	}

}
