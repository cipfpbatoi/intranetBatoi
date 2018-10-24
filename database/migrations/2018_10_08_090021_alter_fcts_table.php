<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AlterFctsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('fcts', function(Blueprint $table)
		{
			$table->dropColumn('calificacion');
                        $table->dropColumn('calProyecto');
                        $table->dropColumn('actas');
                        $table->dropColumn('insercion');
                        $table->dropColumn('hasta');
                        $table->dropColumn('horas_semanales');
                        $table->string('idInstructor', 10)->nullable()->index('instructor_fcts_idinstructor_foreign');
                        $table->dropForeign('fcts_idalumno_foreign');
                        $table->string('idAlumno', 8)->nullable()->change();
                        $table->date('desde')->nullable()->change();
                        $table->foreign('idInstructor')->references('dni')->on('instructores')->onUpdate('CASCADE')->onDelete('RESTRICT');
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
			$table->boolean('calificacion')->nullable();
			$table->boolean('calProyecto')->nullable();
                        $table->tinyInteger('horas_semanales')->default(0);
			$table->boolean('actas')->default(0);
			$table->boolean('insercion')->default(0);
                        $table->date('hasta')->nullable();
                        $table->dropForeign('fcts_idinstructor_foreign');
            	});
                Schema::table('fcts', function(Blueprint $table)
		{
                        $table->dropColumn('idInstructor'); 
                });
                
	}

}
