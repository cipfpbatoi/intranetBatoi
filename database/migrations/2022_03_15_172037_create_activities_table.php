<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateActivitiesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('activities', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('action');
			$table->string('model_class')->nullable();
			$table->string('model_id')->nullable();
			$table->string('author_id')->index();
			$table->timestamps();
			$table->text('comentari', 65535)->nullable();
			$table->text('document', 65535)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('activities');
	}

}
