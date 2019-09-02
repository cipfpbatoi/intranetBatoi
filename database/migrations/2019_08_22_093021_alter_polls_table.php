<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AlterPollsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('polls', function (Blueprint $table) {
            //$table->dropColumn('activo');
            //$table->date('desde')->nullable();
            //$table->date('hasta')->nullable();
            //$table->integer('idModelo')->nullable()->unsigned();
            //$table->integer('idPPoll')->default(1)->unsigned();
            $table->foreign('idPPoll')->references('id')->on('ppolls')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('polls', function (Blueprint $table) {
            $table->tinyInteger('activo')->default(0);
            $table->dropColumn('desde');
            $table->dropColumn('hasta');
            $table->dropForeign('polls_idppoll_foreign');
            $table->dropColumn('idPPoll');
            $table->dropColumn('idModelo');

        });
	}

}
