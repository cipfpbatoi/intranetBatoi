<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateComisionesFctsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('comision_fcts', function(Blueprint $table)
		{
            $table->increments('id');
			$table->integer('idFct')->unsigned();
			$table->integer('idComision')->unsigned();
			$table->string('hora_ini');
			$table->boolean('aviso');
			$table->unique(['idFct','idComision'], 'comision_fcts_idFct_idComision_unique');
            $table->foreign('idFct')->references('id')->on('fcts')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign('idComision')->references('id')->on('comisiones')->onUpdate('CASCADE')->onDelete('CASCADE');

        });
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('comision_fcts', function(Blueprint $table)
        {
            $table->dropForeign('comision_fcts_idfct_foreign');
            $table->dropForeign('comision_fcts_idcomision_foreign');
        });
        Schema::drop('comision_fcts');
	}

}