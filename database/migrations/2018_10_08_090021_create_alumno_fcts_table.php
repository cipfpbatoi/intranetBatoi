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
			$table->string('idAlumno', 8)->collation('utf8_unicode_ci')->index('alumno_fcts_idAlumno_foreign');
			$table->boolean('calificacion')->nullable();
			$table->boolean('calProyecto')->nullable();
			$table->boolean('actas')->default(0);
			$table->boolean('insercion')->default(0);
                        $table->unique(['idFct','idAlumno'], 'alumno_fcts_idFct_idAlumno_unique');
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