<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AlterAlumnoFctsTable4 extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('alumno_fcts', function(Blueprint $table)
		{
            $table->dropColumn('festiusEscolars');
        });
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('fcts', function(Blueprint $table)
		{
            $table->boolean('festiusEscolars')->default(0);
        });
	}

}
