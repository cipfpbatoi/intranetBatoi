<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToProgramacionesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('programaciones', function(Blueprint $table)
		{
			$table->foreign('idModuloCiclo')->references('id')->on('modulo_ciclos')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('programaciones', function(Blueprint $table)
		{
			$table->dropForeign('programaciones_idmodulociclo_foreign');
		});
	}

}
