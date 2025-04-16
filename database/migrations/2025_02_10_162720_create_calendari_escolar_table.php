<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('calendari_escolar', function (Blueprint $table) {
            $table->id();
            $table->date('data')->unique(); // Cada data només pot aparèixer una vegada
            $table->enum('tipus', ['lectiu', 'no lectiu', 'festiu'])->default('lectiu'); // Indica si el dia és lectiu, no lectiu o festiu
            $table->string('esdeveniment')->nullable(); // Nom de l'esdeveniment si n'hi ha
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calendari_escolar');
    }
};
