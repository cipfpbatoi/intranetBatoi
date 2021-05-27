<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AlterActionEventsTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('action_events', function (Blueprint $table) {
            $table->string('user_id',20)->change();
            $table->string('actionable_id',20)->change();
            $table->string('target_id',20)->change();
            $table->string('model_id',20)->nullable()->change();
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
            $table->bigInteger('user_id')->unsigned()->change();
            $table->bigInteger('actionable_id')->unsigned()->change();
            $table->bigInteger('target_id')->unsigned()->change();
            $table->bigInteger('model_id')->unsigned()->nullable()->change();
        });
	}

}
