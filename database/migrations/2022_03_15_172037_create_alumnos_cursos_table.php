<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAlumnosCursosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('alumnos_cursos', function(Blueprint $table)
		{
			$table->string('idAlumno', 8)->index('alumnos_cursos_idalumno_foreign');
			$table->integer('idCurso')->unsigned()->index('alumnos_cursos_idcurso_foreign');
			$table->boolean('finalizado');
			$table->string('registrado', 1)->nullable();
			$table->timestamps();
			$table->increments('id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('alumnos_cursos');
	}

}
