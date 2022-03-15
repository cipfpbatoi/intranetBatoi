<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToComisionesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('comisiones', function(Blueprint $table)
		{
			$table->foreign('idDocumento')->references('id')->on('documentos')->onUpdate('CASCADE')->onDelete('SET NULL');
			$table->foreign('idProfesor')->references('dni')->on('profesores')->onUpdate('NO ACTION')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('comisiones', function(Blueprint $table)
		{
			$table->dropForeign('comisiones_iddocumento_foreign');
			$table->dropForeign('comisiones_idprofesor_foreign');
		});
	}

}
