<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AlterVotesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('votes', function (Blueprint $table) {
            $table->dropForeign('votes_user_id_foreign');

        });
        Schema::table('votes', function (Blueprint $table) {

            $table->string('user_id',250)->change();
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
            $table->string('user_id',8)->change();
        });
	}

}
