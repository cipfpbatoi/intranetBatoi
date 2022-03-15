<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToArticulosLoteTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('articulos_lote', function(Blueprint $table)
		{
			$table->foreign('articulo_id')->references('id')->on('articulos')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('lote_id')->references('registre')->on('lotes')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('articulos_lote', function(Blueprint $table)
		{
			$table->dropForeign('articulos_lote_articulo_id_foreign');
			$table->dropForeign('articulos_lote_lote_id_foreign');
		});
	}

}
