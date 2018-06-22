<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFctsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('fcts', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('idAlumno', 8)->index('fcts_idalumno_foreign');
			$table->integer('idColaboracion')->nullable()->unsigned()->index('fcts_idcolaboracion_foreign');
			$table->boolean('asociacion');
			$table->date('desde');
			$table->date('hasta')->nullable();
			$table->smallInteger('horas');
			$table->smallInteger('horas_semanales')->default(40);
			$table->boolean('calificacion')->nullable();
			$table->boolean('calProyecto')->nullable();
			$table->boolean('actas')->default(0);
			$table->boolean('correoAlumno')->default(0);
			$table->boolean('correoInstructor')->default(0);
			$table->boolean('insercion')->default(0);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('fcts');
	}

}
