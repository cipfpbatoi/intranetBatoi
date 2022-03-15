<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateActionEventsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('action_events', function(Blueprint $table)
		{
			$table->bigInteger('id', true)->unsigned();
			$table->char('batch_id', 36);
			$table->string('user_id', 20)->index();
			$table->string('name');
			$table->string('actionable_type');
			$table->string('actionable_id', 20);
			$table->string('target_type');
			$table->string('target_id', 20);
			$table->string('model_type');
			$table->string('model_id', 20)->nullable();
			$table->text('fields', 65535);
			$table->string('status', 25)->default('running');
			$table->text('exception', 65535);
			$table->timestamps();
			$table->text('original', 16777215)->nullable();
			$table->text('changes', 16777215)->nullable();
			$table->index(['actionable_type','actionable_id']);
			$table->index(['batch_id','model_type','model_id']);
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('action_events');
	}

}
