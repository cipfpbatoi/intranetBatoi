<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToActividadProfesorTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('actividad_profesor', function(Blueprint $table)
		{
			$table->foreign('idActividad')->references('id')->on('actividades')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('idProfesor')->references('dni')->on('profesores')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('actividad_profesor', function(Blueprint $table)
		{
			$table->dropForeign('actividad_profesor_idactividad_foreign');
			$table->dropForeign('actividad_profesor_idprofesor_foreign');
		});
	}

}
