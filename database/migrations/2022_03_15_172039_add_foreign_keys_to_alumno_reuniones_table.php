<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToAlumnoReunionesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('alumno_reuniones', function(Blueprint $table)
		{
			$table->foreign('idAlumno')->references('nia')->on('alumnos')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('idReunion')->references('id')->on('reuniones')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('alumno_reuniones', function(Blueprint $table)
		{
			$table->dropForeign('alumno_reuniones_idalumno_foreign');
			$table->dropForeign('alumno_reuniones_idreunion_foreign');
		});
	}

}
