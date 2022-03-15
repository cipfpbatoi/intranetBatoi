<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToGuardiasTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('guardias', function(Blueprint $table)
		{
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
		Schema::table('guardias', function(Blueprint $table)
		{
			$table->dropForeign('guardias_idprofesor_foreign');
		});
	}

}
