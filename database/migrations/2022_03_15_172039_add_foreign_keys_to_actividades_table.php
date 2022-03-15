<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToActividadesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('actividades', function(Blueprint $table)
		{
			$table->foreign('idDocumento')->references('id')->on('documentos')->onUpdate('CASCADE')->onDelete('SET NULL');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('actividades', function(Blueprint $table)
		{
			$table->dropForeign('actividades_iddocumento_foreign');
		});
	}

}
