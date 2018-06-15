<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateMenusTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('menus', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('nombre', 15);
			$table->string('url', 100)->nullable();
			$table->string('class', 30)->nullable();
			$table->boolean('rol');
			$table->string('menu', 15);
			$table->string('submenu', 15)->nullable();
			$table->boolean('activo')->default(1);
			$table->smallInteger('orden')->default(9999);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('menus');
	}

}
