<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePpollsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ppolls', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('title');
			$table->string('what', 20);
			$table->boolean('anonymous')->default(1);
			$table->boolean('remains')->default(0);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ppolls');
	}

}
