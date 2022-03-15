<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToAlumnosGruposTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('alumnos_grupos', function(Blueprint $table)
		{
			$table->foreign('idAlumno')->references('nia')->on('alumnos')->onUpdate('CASCADE')->onDelete('CASCADE');
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
		Schema::table('alumnos_grupos', function(Blueprint $table)
		{
			$table->dropForeign('alumnos_grupos_idalumno_foreign');
			$table->dropForeign('alumnos_grupos_idgrupo_foreign');
		});
	}

}
