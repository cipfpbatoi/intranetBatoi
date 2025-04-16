<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DeleteFieldsProgramacionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('programaciones', function (Blueprint $table) {
            // Afegeix un nou camp de tipus string anomenat 'flexible'
            // Pots canviar 'string' per un altre tipus de dada si ho necessites
            $table->dropColumn('anexos');
            $table->dropColumn('ciclo');
            $table->dropColumn('curso');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('programaciones', function (Blueprint $table) {
            $table->tinyInteger('anexos');
            $table->string('ciclo',80);
            $table->string('curso',10);
        });
    }
}
