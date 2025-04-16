<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdProfesorAndHoraDefensaToProjectesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('projectes', function (Blueprint $table) {
            $table->string('idProfesor', 10)
                ->collation('utf8mb3_unicode_ci')
                ->nullable()
                ->index();
            $table->foreign('idProfesor')
                ->references('dni')
                ->on('profesores')
                ->onDelete('set null');
            $table->time('hora_defensa')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('projectes', function (Blueprint $table) {
            $table->dropForeign(['idProfesor']);
            $table->dropColumn('idProfesor');
            $table->dropColumn('hora_defensa');
        });
    }
}
