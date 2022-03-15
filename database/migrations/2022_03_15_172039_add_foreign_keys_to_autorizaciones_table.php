<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToAutorizacionesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('autorizaciones', function(Blueprint $table)
		{
			$table->foreign('idActividad')->references('id')->on('actividades')->onUpdate('CASCADE')->onDelete('CASCADE');
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
		Schema::table('autorizaciones', function(Blueprint $table)
		{
			$table->dropForeign('autorizaciones_idactividad_foreign');
			$table->dropForeign('autorizaciones_idalumno_foreign');
		});
	}

}
