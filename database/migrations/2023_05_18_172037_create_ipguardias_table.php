<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateIpguardiasTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
    public function up()
    {
        Schema::create('ipGuardias', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ip', 15);
            $table->string('codOcup', 10);
        });
    }


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ipGuardias');
	}

}
