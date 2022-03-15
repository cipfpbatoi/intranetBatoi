<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAlumnoResultadosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('alumno_resultados', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('idAlumno', 8);
			$table->integer('idModuloGrupo')->unsigned()->index('alumno_resultados_idmodulogrupo_foreign');
			$table->boolean('nota')->default(0);
			$table->boolean('valoraciones')->default(0);
			$table->string('observaciones', 200)->nullable();
			$table->unique(['idAlumno','idModuloGrupo'], 'alumno_resultados_unique');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('alumno_resultados');
	}

}
