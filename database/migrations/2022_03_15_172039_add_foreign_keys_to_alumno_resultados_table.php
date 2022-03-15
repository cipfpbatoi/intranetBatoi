<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToAlumnoResultadosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('alumno_resultados', function(Blueprint $table)
		{
			$table->foreign('idAlumno')->references('nia')->on('alumnos')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('idModuloGrupo')->references('id')->on('modulo_grupos')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('alumno_resultados', function(Blueprint $table)
		{
			$table->dropForeign('alumno_resultados_idalumno_foreign');
			$table->dropForeign('alumno_resultados_idmodulogrupo_foreign');
		});
	}

}
