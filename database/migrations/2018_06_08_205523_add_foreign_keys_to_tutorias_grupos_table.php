<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AddForeignKeysToTutoriasGruposTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('tutorias_grupos', function(Blueprint $table)
		{
			$table->foreign('idGrupo')->references('codigo')->on('grupos')->onUpdate('CASCADE')->onDelete('CASCADE');
			$table->foreign('idTutoria')->references('id')->on('tutorias')->onUpdate('CASCADE')->onDelete('CASCADE');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('tutorias_grupos', function(Blueprint $table)
		{
			$table->dropForeign('tutorias_grupos_idgrupo_foreign');
			$table->dropForeign('tutorias_grupos_idtutoria_foreign');
		});
	}

}
