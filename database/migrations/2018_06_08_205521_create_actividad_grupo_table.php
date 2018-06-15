<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateActividadGrupoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('actividad_grupo', function(Blueprint $table)
		{
			$table->integer('idActividad')->unsigned();
			$table->string('idGrupo', 5)->index('actividad_grupo_idgrupo_foreign');
			$table->primary(['idActividad','idGrupo']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('actividad_grupo');
	}

}
