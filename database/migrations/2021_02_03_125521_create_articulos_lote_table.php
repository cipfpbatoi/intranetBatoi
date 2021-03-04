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
            $table->string('lote_id',12)->nullable();
            $table->integer('articulo_id')->unsigned();
            $table->string('marca', 50)->nullable();
            $table->string('modelo', 50)->nullable();
            $table->smallInteger('unidades')->default(1);
            $table->foreign('lote_id')->references('registre')->on('lotes')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('articulo_id')->references('id')->on('articulos')->onUpdate('CASCADE')->onDelete('CASCADE');
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
