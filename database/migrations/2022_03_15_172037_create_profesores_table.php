<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProfesoresTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('profesores', function(Blueprint $table)
		{
			$table->string('dni', 10)->primary();
			$table->smallInteger('codigo');
			$table->string('nombre', 25);
			$table->string('apellido1', 25);
			$table->string('apellido2', 25);
			$table->string('password', 100);
			$table->string('emailItaca', 45);
			$table->string('email', 45);
			$table->string('domicilio', 45);
			$table->string('movil1', 10);
			$table->string('movil2', 10);
			$table->string('sexo', 1);
			$table->string('codigo_postal', 5)->nullable();
			$table->boolean('departamento')->nullable();
			$table->date('fecha_ingreso')->nullable();
			$table->date('fecha_nac')->nullable();
			$table->date('fecha_baja')->nullable();
			$table->date('fecha_ant')->nullable();
			$table->string('sustituye_a', 10)->nullable();
			$table->string('foto', 60)->nullable();
			$table->bigInteger('rol')->default(3);
			$table->string('remember_token', 100)->nullable();
			$table->timestamps();
			$table->dateTime('last_logged')->nullable();
			$table->boolean('activo')->default(1);
			$table->string('idioma', 2)->default('ca');
			$table->string('api_token', 60);
			$table->boolean('mostrar')->default(0);
			$table->string('especialitat', 100)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('profesores');
	}

}
