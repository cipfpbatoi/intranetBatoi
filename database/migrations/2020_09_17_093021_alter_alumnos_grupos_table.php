<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AlterAlumnosGruposTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('alumnos_grupos', function (Blueprint $table) {
            $table->string('subGrupo', 1);
            $table->string('posicion', 2);
        });
    }


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('alumnos_grupos', function (Blueprint $table) {
            $table->dropColumn('subGrupo');
            $table->dropColumn('posicion');
        });
	}

}
