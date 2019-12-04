<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AlterVotesTable2 extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('votes', function (Blueprint $table) {
            $table->dropForeign('votes_idmodulogrupo_foreign');
            $table->dropForeign('votes_idprofesor_foreign');
            $table->renameColumn('idModuloGrupo', 'idOption1');
            $table->renameColumn('idProfesor', 'idOption2');

         });
    }


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('votes', function (Blueprint $table) {
            $table->renameColumn('idOption1','idModuloGrupo');
            $table->renameColumn('idOption2','idProfesor');
        });
	}

}
