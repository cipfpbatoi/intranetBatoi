<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToMiembrosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('miembros', function(Blueprint $table)
		{
			$table->foreign('idGrupoTrabajo')->references('id')->on('grupos_trabajo')->onUpdate('CASCADE')->onDelete('CASCADE');
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
		Schema::table('miembros', function(Blueprint $table)
		{
			$table->dropForeign('miembros_idgrupotrabajo_foreign');
			$table->dropForeign('miembros_idprofesor_foreign');
		});
	}

}
