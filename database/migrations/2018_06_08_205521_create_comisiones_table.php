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
			$table->text('servicio', 65535);
			$table->decimal('alojamiento', 5)->default(0.00);
			$table->decimal('comida', 5)->default(0.00);
			$table->string('medio', 30);
			$table->decimal('gastos', 5)->default(0.00);
			$table->string('marca', 30);
			$table->string('matricula', 10);
			$table->string('otros', 80)->nullable();
			$table->integer('kilometraje')->unsigned();
			$table->dateTime('desde');
			$table->dateTime('hasta');
			$table->boolean('estado')->default(0);
			$table->boolean('fct')->default(1);
                        $table->string('itinerario', 254)->nullable();
			$table->timestamps();
			
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
