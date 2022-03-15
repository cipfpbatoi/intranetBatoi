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
			$table->foreign('idAlumno')->references('nia')->on('alumnos')->onUpdate('CASCADE')->onDelete('NO ACTION');
			$table->foreign('idDocumento')->references('id')->on('documentos')->onUpdate('CASCADE')->onDelete('SET NULL');
			$table->foreign('idModulo')->references('codigo')->on('modulos')->onUpdate('CASCADE')->onDelete('NO ACTION');
			$table->foreign('idProfesor')->references('dni')->on('profesores')->onUpdate('CASCADE')->onDelete('NO ACTION');
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
			$table->dropForeign('expedientes_iddocumento_foreign');
			$table->dropForeign('expedientes_idmodulo_foreign');
			$table->dropForeign('expedientes_idprofesor_foreign');
		});
	}

}
