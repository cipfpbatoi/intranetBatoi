<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateComisionesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('comisiones', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('idProfesor', 10)->index('comisiones_idprofesor_foreign');
			$table->text('servicio', 65535)->nullable();
			$table->decimal('alojamiento', 5)->default(0.00);
			$table->decimal('comida', 5)->default(0.00);
			$table->string('medio', 30)->nullable();
			$table->decimal('gastos', 5)->default(0.00);
			$table->string('marca', 30)->nullable();
			$table->string('matricula', 10)->nullable();
			$table->string('otros', 80)->nullable();
			$table->integer('kilometraje')->unsigned()->nullable();
			$table->dateTime('desde');
			$table->dateTime('hasta');
			$table->boolean('estado')->default(0);
			$table->boolean('fct')->default(0);
			$table->timestamps();
			$table->string('itinerario', 254)->nullable();
			$table->integer('idDocumento')->unsigned()->nullable()->index('comisiones_iddocumento_foreign');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('comisiones');
	}

}
