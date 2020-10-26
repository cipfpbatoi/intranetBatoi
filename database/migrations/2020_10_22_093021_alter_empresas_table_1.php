<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AlterEmpresasTable1 extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
                Schema::table('empresas', function(Blueprint $table)
		{
                    $table->string('gerente', 100)->nullable();
                });
        }


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
                Schema::table('empresas', function(Blueprint $table)
		{
                    $table->dropColumn('gerente');
                });
	}

}
