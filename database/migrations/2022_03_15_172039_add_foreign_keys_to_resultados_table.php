<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToResultadosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('resultados', function(Blueprint $table)
		{
			$table->foreign('idModuloGrupo')->references('id')->on('modulo_grupos')->onUpdate('CASCADE')->onDelete('CASCADE');
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
		Schema::table('resultados', function(Blueprint $table)
		{
			$table->dropForeign('resultados_idmodulogrupo_foreign');
			$table->dropForeign('resultados_idprofesor_foreign');
		});
	}

}
