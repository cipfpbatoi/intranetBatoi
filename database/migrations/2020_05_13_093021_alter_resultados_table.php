<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AlterResultadosTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('resultados', function (Blueprint $table) {
            $table->text('adquiridosSI', 65535)->nullable();
            $table->text('adquiridosNO', 65535)->nullable();
        });
    }


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('resultados', function (Blueprint $table) {
            $table->dropColumn('adquiridosSI');
            $table->dropColumn('adquiridosNO');
        });
	}

}
