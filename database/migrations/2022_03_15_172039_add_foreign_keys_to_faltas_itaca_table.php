<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToFaltasItacaTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('faltas_itaca', function(Blueprint $table)
		{
			$table->foreign('idDocumento')->references('id')->on('documentos')->onUpdate('CASCADE')->onDelete('SET NULL');
			$table->foreign('idGrupo')->references('codigo')->on('grupos')->onUpdate('CASCADE')->onDelete('CASCADE');
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
		Schema::table('faltas_itaca', function(Blueprint $table)
		{
			$table->dropForeign('faltas_itaca_iddocumento_foreign');
			$table->dropForeign('faltas_itaca_idgrupo_foreign');
			$table->dropForeign('faltas_itaca_idprofesor_foreign');
		});
	}

}
