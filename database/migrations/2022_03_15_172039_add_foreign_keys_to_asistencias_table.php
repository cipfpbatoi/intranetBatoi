<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToAsistenciasTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('asistencias', function(Blueprint $table)
		{
			$table->foreign('idProfesor')->references('dni')->on('profesores')->onUpdate('CASCADE')->onDelete('NO ACTION');
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
		Schema::table('asistencias', function(Blueprint $table)
		{
			$table->dropForeign('asistencias_idprofesor_foreign');
			$table->dropForeign('asistencias_idreunion_foreign');
		});
	}

}
