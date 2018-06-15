<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToActividadGrupoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('actividad_grupo', function(Blueprint $table)
		{
			$table->foreign('idActividad')->references('id')->on('actividades')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('idGrupo')->references('codigo')->on('grupos')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('actividad_grupo', function(Blueprint $table)
		{
			$table->dropForeign('actividad_grupo_idactividad_foreign');
			$table->dropForeign('actividad_grupo_idgrupo_foreign');
		});
	}

}
