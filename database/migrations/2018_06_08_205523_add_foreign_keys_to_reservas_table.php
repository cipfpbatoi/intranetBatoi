<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToReservasTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('reservas', function(Blueprint $table)
		{
			$table->foreign('idEspacio')->references('aula')->on('espacios')->onUpdate('CASCADE')->onDelete('CASCADE');
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
		Schema::table('reservas', function(Blueprint $table)
		{
			$table->dropForeign('reservas_idespacio_foreign');
			$table->dropForeign('reservas_idprofesor_foreign');
		});
	}

}
