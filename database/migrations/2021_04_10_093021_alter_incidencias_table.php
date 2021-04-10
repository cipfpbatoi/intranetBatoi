<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AlterIncidenciasTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('incidencias', function (Blueprint $table) {
            $table->renameColumn('Observaciones', 'observaciones');
        });
    }


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('incidencias', function (Blueprint $table) {
            $table->renameColumn('observaciones', 'Observaciones');
        });
	}

}
