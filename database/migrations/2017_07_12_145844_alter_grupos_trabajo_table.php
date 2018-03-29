<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterGruposTrabajoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('grupos_trabajo', function (Blueprint $table) {
            $table->string('literal',40);
            $table->dropColumn('cliteral');
            $table->dropColumn('vliteral');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $table->dropColumn('literal');
        $table->string('cliteral',40);
        $table->string('vliteral',40);
    }
}
