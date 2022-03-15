<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToTipoincidenciasTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('tipoincidencias', function(Blueprint $table)
		{
			$table->foreign('idProfesor')->references('dni')->on('profesores')->onUpdate('NO ACTION')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('tipoincidencias', function(Blueprint $table)
		{
			$table->dropForeign('tipoincidencias_idprofesor_foreign');
		});
	}

}
