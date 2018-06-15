<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterModulociclosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modulo_ciclos', function (Blueprint $table) {
//            $table->charset = 'utf8';
//            $table->collation = 'utf8_unicode_ci';
            $table->tinyInteger('idDepartamento')->nullable();
            $table->foreign('idDepartamento')->references('id')->on('departamentos')
                    ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('modulo_ciclos', function (Blueprint $table) {
            $table->dropForeign('modulo_ciclos_iddepartamento_foreign');
            $table->dropColumn('idDepartamento');
        });
    }
}
