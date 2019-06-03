<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToAlumnoFctsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('alumno_fcts', function(Blueprint $table)
		{
			$table->foreign('idFct')->references('id')->on('fcts')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('idAlumno')->references('nia')->on('alumnos')->onUpdate('CASCADE')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('alumno_fcts', function(Blueprint $table)
		{
			$table->dropForeign('alumno_fcts_idfct_foreign');
			$table->dropForeign('alumno_fcts_idalumno_foreign');
		});
	}

}
