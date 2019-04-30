<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AlterCiclosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
                Schema::table('ciclos', function(Blueprint $table)
		{
                    $table->smallInteger('horasFct')->default(400);
                });
        }


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
                Schema::table('ciclos', function(Blueprint $table)
		{
                    $table->smallInteger('horasFct');
                });
	}

}
