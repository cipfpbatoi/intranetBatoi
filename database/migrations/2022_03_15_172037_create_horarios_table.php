<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateHorariosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('horarios', function(Blueprint $table)
		{
			$table->increments('id');
            $table->enum('dia_semana', array('L','M','X','J','V'));
			$table->boolean('sesion_orden');
			$table->string('idProfesor', 10);
			$table->string('modulo', 12)->nullable();
			$table->string('idGrupo', 5)->nullable();
			$table->string('aula', 5)->nullable();
			$table->string('ocupacion', 10)->nullable();
			$table->timestamps();
			$table->integer('plantilla');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('horarios');
	}

}
