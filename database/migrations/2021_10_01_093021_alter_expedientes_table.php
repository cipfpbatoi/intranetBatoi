<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AlterExpedientesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tipo_expedientes', function (Blueprint $table) {
            $table->text('vista')->nullable();
        });
    }


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('tipo_expedientes', function(Blueprint $table){
            $table->dropColumn('vista');
        });
	}

}
