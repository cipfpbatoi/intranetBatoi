<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateLotesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('lotes', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('descripcion');
			$table->boolean('procedencia')->nullable();
			$table->smallInteger('unidades')->default(0);
			$table->string('proveedor', 90)->nullable();
			$table->string('registre',12)->nullable();
			$table->boolean('inventariable')->default(1);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('lotes');
	}

}
