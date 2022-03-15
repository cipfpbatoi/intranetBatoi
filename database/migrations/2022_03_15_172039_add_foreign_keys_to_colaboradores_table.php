<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToColaboradoresTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('colaboradores', function(Blueprint $table)
		{
			$table->foreign('idFct', 'instructor_fcts_idfct_foreign')->references('id')->on('fcts')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('idInstructor', 'instructor_fcts_idinstructor_foreign')->references('dni')->on('instructores')->onUpdate('CASCADE')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('colaboradores', function(Blueprint $table)
		{
			$table->dropForeign('instructor_fcts_idfct_foreign');
			$table->dropForeign('instructor_fcts_idinstructor_foreign');
		});
	}

}
