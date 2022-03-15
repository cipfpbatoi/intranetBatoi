<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateArticulosLoteTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('articulos_lote', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('lote_id', 12)->nullable()->index('articulos_lote_lote_id_foreign');
			$table->integer('articulo_id')->unsigned()->index('articulos_lote_articulo_id_foreign');
			$table->string('marca', 50)->nullable();
			$table->string('modelo', 50)->nullable();
			$table->smallInteger('unidades')->default(1);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('articulos_lote');
	}

}
