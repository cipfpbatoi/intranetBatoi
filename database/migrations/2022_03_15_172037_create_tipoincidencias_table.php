<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTipoincidenciasTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tipoincidencias', function(Blueprint $table)
		{
			$table->boolean('id')->primary();
			$table->string('nombre', 40);
			$table->string('nom', 40);
			$table->string('idProfesor', 10)->nullable()->index('tipoincidencias_idprofesor_index');
			$table->boolean('tipus')->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('tipoincidencias');
	}

}
