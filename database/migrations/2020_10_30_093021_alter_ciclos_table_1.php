<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AlterCiclosTable1 extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
                Schema::table('ciclos', function(Blueprint $table)
		{
                    $table->string('acronim',10)->nullable();
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
                    $table->dropColumn('acronim');
                });
	}

}
