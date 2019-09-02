<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AlterOptionsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('options', function (Blueprint $table) {
            $table->dropForeign('options_poll_id_foreign');
            $table->dropColumn('poll_id');
            $table->integer('ppoll_id')->default(1)->unsigned();
            $table->foreign('ppoll_id')->references('id')->on('ppolls')->onUpdate('CASCADE')->onDelete('CASCADE');

        });
    }


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('options', function (Blueprint $table) {
            $table->unsignedInteger('poll_id');
            $table->dropForeign('options_ppoll_id_foreign');
            $table->dropColumn('ppoll_id');
            $table->foreign('poll_id')->references('id')->on('polls');
        });
	}

}
