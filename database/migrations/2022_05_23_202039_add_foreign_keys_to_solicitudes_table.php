<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToSolicitudesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('solicitudes', function(Blueprint $table)
		{
			$table->foreign('idAlumno')->references('nia')->on('alumnos')->onUpdate('CASCADE')->onDelete('NO ACTION');
			$table->foreign('idProfesor')->references('dni')->on('profesores')->onUpdate('CASCADE')->onDelete('NO ACTION');
            $table->foreign('idOrientador')->references('dni')->on('profesores')->onUpdate('CASCADE')->onDelete('NO ACTION');
        });
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('expedientes', function(Blueprint $table)
		{
			$table->dropForeign('expedientes_idalumno_foreign');
			$table->dropForeign('expedientes_idorientador_foreign');
			$table->dropForeign('expedientes_idprofesor_foreign');
		});
	}

}
