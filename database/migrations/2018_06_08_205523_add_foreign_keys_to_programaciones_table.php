<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToProgramacionesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('programaciones', function(Blueprint $table)
		{
			$table->foreign('idModuloCiclo')->references('id')->on('modulo_ciclos')->onUpdate('CASCADE')->onDelete('RESTRICT');
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
		Schema::table('programaciones', function(Blueprint $table)
		{
			$table->dropForeign('programaciones_idmodulociclo_foreign');
			$table->dropForeign('programaciones_idprofesor_foreign');
		});
	}

}
