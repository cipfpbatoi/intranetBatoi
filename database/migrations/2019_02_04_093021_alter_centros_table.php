<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AlterCentrosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
                Schema::table('centros', function(Blueprint $table)
		{
                    $table->string('idioma', 2)->nullable();
                });
        }


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
                Schema::table('centros', function(Blueprint $table)
		{
                    $table->dropColumn('idioma');
                });
	}

}
