<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToExpedientesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('expedientes', function(Blueprint $table)
		{
			$table->foreign('idAlumno')->references('nia')->on('alumnos')->onUpdate('CASCADE')->onDelete('RESTRICT');
			$table->foreign('idModulo')->references('codigo')->on('modulos')->onUpdate('CASCADE')->onDelete('RESTRICT');
			$table->foreign('idProfesor')->references('dni')->on('profesores')->onUpdate('CASCADE')->onDelete('RESTRICT');
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
			$table->dropForeign('expedientes_idmodulo_foreign');
			$table->dropForeign('expedientes_idprofesor_foreign');
		});
	}

}
