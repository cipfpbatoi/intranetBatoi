<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTipoExpedientesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tipo_expedientes', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('titulo');
			$table->boolean('rol');
			$table->boolean('orientacion')->default(0);
			$table->boolean('informe')->default(0);
			$table->text('vista', 65535)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('tipo_expedientes');
	}

}
