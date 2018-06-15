<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAlumnosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('alumnos', function(Blueprint $table)
		{
			$table->string('nia', 8)->primary();
			$table->string('dni', 10)->unique();
			$table->string('nombre', 25);
			$table->string('apellido1', 25);
			$table->string('apellido2', 25);
			$table->string('password', 100);
			$table->string('email', 45);
			$table->string('expediente', 10)->nullable();
			$table->string('domicilio', 90);
			$table->string('provincia', 2)->nullable();
			$table->string('municipio', 5)->nullable();
			$table->string('telef1', 14);
			$table->string('telef2', 14)->nullable();
			$table->string('sexo', 1);
			$table->date('fecha_nac');
			$table->string('codigo_postal', 5)->nullable();
			$table->date('fecha_ingreso')->nullable();
			$table->date('fecha_matricula');
			$table->string('foto', 60)->nullable();
			$table->boolean('repite');
			$table->string('turno', 1);
			$table->string('trabaja', 1)->nullable();
			$table->integer('rol')->default(5);
			$table->string('remember_token', 100)->nullable();
			$table->dateTime('last_logged')->nullable();
			$table->date('baja')->nullable();
			$table->string('idioma', 2)->default('ca');
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
		Schema::drop('alumnos');
	}

}
