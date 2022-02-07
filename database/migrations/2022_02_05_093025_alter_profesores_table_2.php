<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AlterProfesoresTable2 extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('profesores', function (Blueprint $table) {
            $table->bigInteger('rol')->change();
        });
    }


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('profesores', function (Blueprint $table) {
            $table->Integer('rol')->change();
        });
	}

}
