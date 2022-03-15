<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToIncidenciasTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('incidencias', function(Blueprint $table)
		{
			$table->foreign('espacio')->references('aula')->on('espacios')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('idProfesor')->references('dni')->on('profesores')->onUpdate('CASCADE')->onDelete('NO ACTION');
			$table->foreign('orden')->references('id')->on('ordenes_trabajo')->onUpdate('CASCADE')->onDelete('SET NULL');
			$table->foreign('tipo')->references('id')->on('tipoincidencias')->onUpdate('CASCADE')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('incidencias', function(Blueprint $table)
		{
			$table->dropForeign('incidencias_espacio_foreign');
			$table->dropForeign('incidencias_idprofesor_foreign');
			$table->dropForeign('incidencias_orden_foreign');
			$table->dropForeign('incidencias_tipo_foreign');
		});
	}

}
