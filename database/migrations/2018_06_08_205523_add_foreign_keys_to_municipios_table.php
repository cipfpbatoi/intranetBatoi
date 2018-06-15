<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToMunicipiosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('municipios', function(Blueprint $table)
		{
			$table->foreign('provincias_id', 'fk_municipios_provincias1')->references('id')->on('provincias')->onUpdate('NO ACTION')->onDelete('NO ACTION');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('municipios', function(Blueprint $table)
		{
			$table->dropForeign('fk_municipios_provincias1');
		});
	}

}
