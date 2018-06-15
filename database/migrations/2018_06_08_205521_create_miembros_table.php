<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMiembrosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('miembros', function(Blueprint $table)
		{
			$table->integer('idGrupoTrabajo')->unsigned();
			$table->string('idProfesor', 10)->index('miembros_idprofesor_foreign');
			$table->boolean('coordinador')->default(0);
			$table->primary(['idGrupoTrabajo','idProfesor']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('miembros');
	}

}
