<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AlterTipoIncidenciasTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tipoincidencias', function (Blueprint $table) {
            $table->string('idProfesor', 10)->nullable()->collation('utf8_unicode_ci')->index();
            $table->tinyInteger('tipus')->nullable();
            $table->foreign('idProfesor')->references('dni')->on('profesores')->onUpdate('RESTRICT')->onDelete('CASCADE');
        });
    }


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('tipoincidencias', function(Blueprint $table){
            $table->dropForeign('tipoincidencias_idprofesor_foreign');
            $table->dropColumn('idProfesor');
            $table->dropColumn('tipus');

        });
	}

}
