<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AlterLotesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lotes', function (Blueprint $table) {
            $table->string('factura',15)->nullable();
        });
    }


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('lotes', function(Blueprint $table){
            $table->dropColumn('factura');
        });
	}

}
