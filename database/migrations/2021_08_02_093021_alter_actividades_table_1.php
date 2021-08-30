<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AlterActividadesTable1 extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('actividades', function (Blueprint $table) {
            $table->text('desenvolupament')->nullable();
            $table->text('valoracio')->nullable();
            $table->text('aspectes')->nullable();
            $table->text('dades')->nullable();
            $table->boolean('recomanada')->default(1);
            $table->boolean('poll')->default(1);
        });
    }


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('actividades', function(Blueprint $table){
            $table->dropColumn('desenvolupament');
            $table->dropColumn('valoracio');
            $table->dropColumn('aspectes');
            $table->dropColumn('dades');
            $table->dropColumn('recomanada');
            $table->dropColumn('poll');
        });
	}

}
