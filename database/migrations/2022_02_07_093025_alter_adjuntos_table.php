<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AlterAdjuntosTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('adjuntos', function (Blueprint $table) {
            $table->string('route','60');
            $table->string('name',100)->change();
            $table->dropColumn('model');
            $table->dropColumn('model_id');
        });
    }


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('adjuntos', function (Blueprint $table) {
            $table->dropColumn('route',);
            $table->string('model',20);
            $table->bigInteger('model_id');
        });
	}

}
