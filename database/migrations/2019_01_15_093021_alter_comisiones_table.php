<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AlterComisionesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
                
		
                Schema::table('comisiones', function(Blueprint $table)
		{
                    $table->text('servicio', 65535)->nullable()->change();
                    $table->string('medio', 30)->nullable()->change();
                    $table->string('marca', 30)->nullable()->change();
                    $table->string('matricula', 10)->nullable()->change();
                    $table->integer('kilometraje')->unsigned()->nullable()->change();
                });
                

	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
                Schema::table('comisiones', function(Blueprint $table)
		{
                    $table->text('servicio', 65535)->change();
                    $table->string('medio', 30)->change();
                    $table->string('marca', 30)->change();
                    $table->string('matricula', 10)->change();
                    $table->integer('kilometraje')->unsigned()->change();
                });
	}

}
