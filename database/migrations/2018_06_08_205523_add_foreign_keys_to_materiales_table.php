<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToMaterialesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('materiales', function(Blueprint $table)
		{
			$table->foreign('espacio')->references('aula')->on('espacios')->onUpdate('CASCADE')->onDelete('RESTRICT');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('materiales', function(Blueprint $table)
		{
			$table->dropForeign('materiales_espacio_foreign');
		});
	}

}
