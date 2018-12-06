<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class AlterEstadosTables extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('actividades', function(Blueprint $table) {
            $table->integer('idDocumento')->unsigned()->nullable();
            $table->foreign('idDocumento')->references('id')->on('documentos')->onUpdate('CASCADE')->onDelete('SET NULL');
	});
        Schema::table('comisiones', function(Blueprint $table) {
            $table->integer('idDocumento')->unsigned()->nullable();
            $table->foreign('idDocumento')->references('id')->on('documentos')->onUpdate('CASCADE')->onDelete('SET NULL');
	});
        Schema::table('expedientes', function(Blueprint $table) {
            $table->integer('idDocumento')->unsigned()->nullable();
            $table->foreign('idDocumento')->references('id')->on('documentos')->onUpdate('CASCADE')->onDelete('SET NULL');
	});
        Schema::table('faltas', function(Blueprint $table) {
            $table->integer('idDocumento')->unsigned()->nullable();
            $table->foreign('idDocumento')->references('id')->on('documentos')->onUpdate('CASCADE')->onDelete('SET NULL');
	});
        Schema::table('faltas_itaca', function(Blueprint $table) {
            $table->integer('idDocumento')->unsigned()->nullable();
            $table->foreign('idDocumento')->references('id')->on('documentos')->onUpdate('CASCADE')->onDelete('SET NULL');
	});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('actividades', function(Blueprint $table) {
            $table->dropForeign('actividades_iddocumento_foreign');
            $table->dropColumn('idDocumento');
            
        });
        Schema::table('comisiones', function(Blueprint $table) {
            $table->dropForeign('comisiones_iddocumento_foreign');
            $table->dropColumn('idDocumento');
            
        });
        Schema::table('expedientes', function(Blueprint $table) {
            $table->dropForeign('expedientes_iddocumento_foreign');
            $table->dropColumn('idDocumento');
            
        });
        Schema::table('faltas', function(Blueprint $table) {
            $table->dropForeign('faltas_iddocumento_foreign');
            $table->dropColumn('idDocumento');
            
        });
        Schema::table('faltas_itaca', function(Blueprint $table) {
            $table->dropForeign('faltas_itaca_iddocumento_foreign');
            $table->dropColumn('idDocumento');
            
        });
    }

}
