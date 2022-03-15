<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToReunionesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('reuniones', function(Blueprint $table)
		{
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
		Schema::table('reuniones', function(Blueprint $table)
		{
			$table->dropForeign('reuniones_idprofesor_foreign');
		});
	}

}
