<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAlumnoFctsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('alumno_fcts', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('idFct')->unsigned();
			$table->string('idAlumno', 8)->index('alumno_fcts_idAlumno_foreign');
			$table->boolean('calificacion')->nullable();
			$table->boolean('calProyecto')->nullable();
			$table->boolean('actas')->default(0);
			$table->boolean('insercion')->default(0);
			$table->smallInteger('horas')->nullable();
			$table->date('desde')->nullable();
			$table->date('hasta')->nullable();
			$table->boolean('correoAlumno')->default(0);
			$table->boolean('pg0301')->default(0);
			$table->float('beca')->default(0.00);
			$table->boolean('a56')->default(0);
			$table->unique(['idFct','idAlumno']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('alumno_fcts');
	}

}
