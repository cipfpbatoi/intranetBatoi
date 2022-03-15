<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToTasksProfesoresTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('tasks_profesores', function(Blueprint $table)
		{
			$table->foreign('id_profesor')->references('dni')->on('profesores')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('id_task')->references('id')->on('tasks')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('tasks_profesores', function(Blueprint $table)
		{
			$table->dropForeign('tasks_profesores_id_profesor_foreign');
			$table->dropForeign('tasks_profesores_id_task_foreign');
		});
	}

}
