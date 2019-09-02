<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AlterVotesTable1 extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('votes', function (Blueprint $table) {
            $table->integer('idPoll')->unsigned();
            $table->foreign('idPoll')->references('id')->on('polls')->onUpdate('CASCADE')->onDelete('CASCADE');


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
            $table->dropForeign('votes_idpoll_foreign');
            $table->dropColumn('idPoll');
        });
	}

}
