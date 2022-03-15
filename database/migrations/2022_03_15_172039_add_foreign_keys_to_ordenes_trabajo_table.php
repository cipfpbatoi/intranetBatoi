<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToOrdenesTrabajoTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('ordenes_trabajo', function(Blueprint $table)
		{
			$table->foreign('idProfesor')->references('dni')->on('profesores')->onUpdate('CASCADE')->onDelete('SET NULL');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('ordenes_trabajo', function(Blueprint $table)
		{
			$table->dropForeign('ordenes_trabajo_idprofesor_foreign');
		});
	}

}
