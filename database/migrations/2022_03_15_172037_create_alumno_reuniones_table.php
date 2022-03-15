<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAlumnoReunionesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('alumno_reuniones', function(Blueprint $table)
		{
			$table->integer('id', true);
			$table->integer('idReunion')->unsigned()->index('asistencias_idreunion_foreign');
			$table->string('idAlumno', 8)->index('asistencias_idAlumno_foreign');
			$table->boolean('capacitats')->default(0);
			$table->boolean('sent')->default(0);
			$table->string('token', 60)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('alumno_reuniones');
	}

}
