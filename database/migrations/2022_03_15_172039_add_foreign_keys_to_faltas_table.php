<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToFaltasTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('faltas', function(Blueprint $table)
		{
			$table->foreign('idDocumento')->references('id')->on('documentos')->onUpdate('CASCADE')->onDelete('SET NULL');
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
		Schema::table('faltas', function(Blueprint $table)
		{
			$table->dropForeign('faltas_iddocumento_foreign');
			$table->dropForeign('faltas_idprofesor_foreign');
		});
	}

}
