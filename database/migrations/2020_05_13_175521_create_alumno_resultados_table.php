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
            $table->string('idAlumno', 8)->collation('utf8_unicode_ci');
            $table->integer('idModuloGrupo')->unsigned();
            $table->tinyInteger('nota')->default(0);
            $table->text('recomendaciones')->nullable();
			$table->unique(['idAlumno','idModuloGrupo'], 'alumno_resultados_unique');
            $table->foreign('idModuloGrupo')->references('id')->on('modulo_grupos')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('idAlumno')->references('nia')->on('alumnos')->onUpdate('CASCADE')->onDelete('CASCADE');

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
