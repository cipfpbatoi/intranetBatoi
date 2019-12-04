<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AlterPPollsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ppolls', function (Blueprint $table) {
            $table->dropColumn('who');
            $table->string('what',20)->change();
        });
    }


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('ppolls', function (Blueprint $table) {
            $table->tinyInteger('who')->default(1);
            $table->tinyInteger('what')->default(1)->change();
        });
	}

}
