<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AlterMaterialesTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('materiales', function (Blueprint $table) {
            $table->boolean('inventariable')->default(0);
            $table->string('registre',12)->nullable();
            $table->integer('articulo_lote_id')->unsigned()->nullable();
            $table->foreign('articulo_lote_id')->references('id')->on('articulos_lote')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('materiales', function (Blueprint $table) {
            $table->dropColumn('registre');
            $table->dropColumn('inventariable');
            $table->dropForeign('materiales_articulo_lote_id_foreign');
            $table->dropColumn('articulo_lote_id');
        });
	}

}
