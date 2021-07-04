<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AlterModulosTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modulos', function (Blueprint $table) {
            $table->string('codigo',10)->change();
        });
        Schema::table('modulo_ciclos', function (Blueprint $table) {
            $table->string('idModulo',10)->change();
        });
        Schema::table('horarios', function (Blueprint $table) {
            $table->string('modulo',10)->change();
        });
        Schema::table('expedientes', function (Blueprint $table) {
            $table->string('idModulo',10)->change();
        });
    }


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('action_events', function (Blueprint $table) {
            $table->string('codigo',6)->change();
        });
        Schema::table('modulo_ciclos', function (Blueprint $table) {
            $table->string('idModulo',6)->change();
        });
        Schema::table('horarios', function (Blueprint $table) {
            $table->string('modulo',6)->change();
        });
        Schema::table('expedientes', function (Blueprint $table) {
            $table->string('idModulo',6)->change();
        });
	}

}
