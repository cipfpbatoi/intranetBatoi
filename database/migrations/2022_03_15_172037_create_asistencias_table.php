<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAsistenciasTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('asistencias', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('idReunion')->unsigned()->index('asistencias_idreunion_foreign');
			$table->string('idProfesor', 10)->index('asistencias_idprofesor_foreign');
			$table->boolean('asiste')->nullable()->default(1);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('asistencias');
	}

}
