<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea la taula per a registar els cicles formatius que l'alumne ha cursat anteriorment.
     * Permet cicles ja extints (FK opcional a cicles).
     */
    public function up(): void
    {
        Schema::create('cicles_formatius_cursats', function (Blueprint $table) {
            $table->charset = 'utf8mb3';
            $table->collation = 'utf8mb3_unicode_ci';
            $table->id();
            $table->string('alumno_id', 8);
            $table->integer('cicle_formatiu_id')->nullable();
            $table->integer('any_curs');
            $table->timestamps();

            $table->foreign('alumno_id')->references('nia')->on('alumnos')
                ->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('cicle_formatiu_id')->references('id')->on('ciclos')
                ->cascadeOnUpdate()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cicles_formatius_cursats');
    }
};
