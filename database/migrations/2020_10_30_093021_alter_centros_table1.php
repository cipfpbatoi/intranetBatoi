<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AlterCentrosTable1 extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
                Schema::table('centros', function(Blueprint $table)
		{
                    $table->string('codiPostal', 5)->nullable();
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
                    $table->dropColumn('codiPostal');
                });
	}

}
