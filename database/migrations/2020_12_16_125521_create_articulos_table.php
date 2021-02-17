<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateArticulosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('articulos', function(Blueprint $table)
		{
			$table->increments('id');
            $table->string('lote_registre',12)->nullable();
            $table->string('descripcion', 200)->nullable();
            $table->string('marca', 50)->nullable();
            $table->string('modelo', 50)->nullable();
            $table->smallInteger('unidades')->default(1);
            $table->foreign('lote_registre')->references('registre')->on('lotes')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::drop('articulos');
	}

}
