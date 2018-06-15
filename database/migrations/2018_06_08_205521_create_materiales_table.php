<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMaterialesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('materiales', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('nserieprov', 50)->nullable();
			$table->string('descripcion');
			$table->string('marca', 50)->nullable();
			$table->string('modelo', 50)->nullable();
			$table->boolean('procedencia')->nullable();
			$table->boolean('estado')->default(1);
			$table->string('espacio', 10)->index('espacios');
			$table->smallInteger('unidades')->default(1);
			$table->string('ISBN', 35)->nullable();
			$table->date('fechaultimoinventario')->nullable();
			$table->date('fechabaja')->nullable();
			$table->smallInteger('tipo')->nullable();
			$table->string('proveedor', 30)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('materiales');
	}

}
