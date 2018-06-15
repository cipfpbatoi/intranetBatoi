<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToOrdenesReunionesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('ordenes_reuniones', function(Blueprint $table)
		{
			$table->foreign('idReunion')->references('id')->on('reuniones')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('ordenes_reuniones', function(Blueprint $table)
		{
			$table->dropForeign('ordenes_reuniones_idreunion_foreign');
		});
	}

}
