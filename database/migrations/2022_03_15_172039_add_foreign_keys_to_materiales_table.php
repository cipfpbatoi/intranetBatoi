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
			$table->foreign('articulo_lote_id')->references('id')->on('articulos_lote')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('espacio')->references('aula')->on('espacios')->onUpdate('CASCADE')->onDelete('NO ACTION');
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
			$table->dropForeign('materiales_articulo_lote_id_foreign');
			$table->dropForeign('materiales_espacio_foreign');
		});
	}

}
