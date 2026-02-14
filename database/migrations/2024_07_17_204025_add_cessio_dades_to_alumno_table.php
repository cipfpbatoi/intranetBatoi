<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCessioDadesToAlumnoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('alumnos', function (Blueprint $table) {
            // Afegeix un nou camp de tipus string anomenat 'flexible'
            // Pots canviar 'string' per un altre tipus de dada si ho necessites
            $table->boolean('imageRightAccept')->default(0);
            $table->boolean('outOfSchoolActivityAccept')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('alumnos', function (Blueprint $table) {
            $table->dropColumn('imageRightAccept');
            $table->dropColumn('outOfSchoolActivityAccept');
        });
    }
}
