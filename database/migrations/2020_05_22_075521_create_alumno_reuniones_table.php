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
			$table->string('idAlumno', 8)->collation('utf8mb4_unicode_ci')->index('asistencias_idAlumno_foreign');
			$table->tinyInteger('capacitats')->default(0);
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
		Schema::drop('alumno_reuniones');
	}

}
