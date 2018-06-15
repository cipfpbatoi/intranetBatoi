<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCursosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cursos', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('titulo', 150);
			$table->string('tipo', 1)->nullable();
			$table->text('comentarios', 65535);
			$table->text('profesorado', 65535)->nullable();
			$table->string('activo', 1);
			$table->boolean('horas');
			$table->date('fecha_inicio');
			$table->date('fecha_fin');
			$table->timestamps();
			$table->time('hora_ini');
			$table->time('hora_fin');
			$table->smallInteger('aforo')->nullable();
			$table->string('fichero', 100)->nullable();
			$table->boolean('archivada')->default(0);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('cursos');
	}

}
