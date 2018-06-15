<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToCentrosInstructoresTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('centros_instructores', function(Blueprint $table)
		{
			$table->foreign('idCentro')->references('id')->on('centros')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('idInstructor')->references('dni')->on('instructores')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('centros_instructores', function(Blueprint $table)
		{
			$table->dropForeign('centros_instructores_idcentro_foreign');
			$table->dropForeign('centros_instructores_idinstructor_foreign');
		});
	}

}
