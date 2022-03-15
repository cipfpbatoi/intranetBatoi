<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAlumnosGruposTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('alumnos_grupos', function(Blueprint $table)
		{
			$table->string('idAlumno', 8);
			$table->string('idGrupo', 5)->index('alumnos_grupos_idgrupo_foreign');
			$table->string('subGrupo', 1)->nullable();
			$table->string('posicion', 2)->nullable();
			$table->primary(['idAlumno','idGrupo']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('alumnos_grupos');
	}

}
