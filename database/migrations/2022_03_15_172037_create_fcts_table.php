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
			$table->integer('idColaboracion')->unsigned()->nullable()->index('fcts_idcolaboracion_foreign');
			$table->boolean('asociacion');
			$table->date('desde')->nullable();
			$table->boolean('correoInstructor')->default(0);
			$table->string('idInstructor', 10)->nullable()->index('instructor_fcts_idinstructor_foreign');
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
