<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AlterFctsTable2 extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('fcts', function(Blueprint $table)
		{
                    $table->dropColumn('hasta');
                    $table->dropColumn('horas');
                    $table->dropColumn('idAlumno');
			
			
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
			$table->date('hasta')->nullable();
                        $table->smallInteger('horas');
                        $table->string('idAlumno', 8);
		});
	}

}
