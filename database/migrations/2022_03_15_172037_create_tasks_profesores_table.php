<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateTasksProfesoresTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('tasks_profesores', function(Blueprint $table)
		{
			$table->bigInteger('id_task')->unsigned()->index('tasks_profesores_id_task_foreign');
			$table->string('id_profesor', 10)->index('tasks_profesores_id_profesor_foreign');
			$table->boolean('check')->default(0);
			$table->boolean('valid')->default(0);
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
		Schema::drop('tasks_profesores');
	}

}
