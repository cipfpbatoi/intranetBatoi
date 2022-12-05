<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCentrosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('centros', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('idEmpresa')->unsigned()->index('centros_idempresa_foreign');
			$table->string('direccion', 100)->default('');
			$table->string('localidad', 30)->nullable();
			$table->string('instructor', 100)->nullable();
			$table->string('email', 50)->nullable();
			$table->string('telefono', 20)->nullable();
			$table->string('dni', 10)->nullable();
			$table->text('observaciones', 65535)->nullable();
			$table->timestamps();
			$table->string('nombre', 100);
			$table->string('horarios', 200)->nullable();
			$table->string('idioma', 2)->nullable();
			$table->string('codiPostal', 5)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('centros');
	}

}
