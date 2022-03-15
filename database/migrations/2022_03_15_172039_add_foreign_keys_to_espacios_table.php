<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToEspaciosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('espacios', function(Blueprint $table)
		{
			$table->foreign('idDepartamento')->references('id')->on('departamentos')->onUpdate('CASCADE')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('espacios', function(Blueprint $table)
		{
			$table->dropForeign('espacios_iddepartamento_foreign');
		});
	}

}
