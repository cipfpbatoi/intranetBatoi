<?php
// database/migrations/2025_10_02_000000_create_bustia_violeta_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('bustia_violeta', function (Blueprint $table) {
            $table->id();
            // Identitat opcional (si es marca anònim, açò queda null)
            $table->string('dni', 12)->nullable()->index();
            $table->enum('rol', ['alumno','profesor'])->nullable()->index(); // per al filtre
            $table->boolean('anonimo')->default(false);

            // Per a mostrar (còmode per a llistats) sense reconsultes
            $table->string('autor_nombre')->nullable(); // ShortName cache

            // Contingut
            $table->string('categoria')->nullable(); // p.ex.: "assetjament","igualtat","altres"
            $table->text('mensaje');

            // Metadades i moderació
            $table->enum('estado', ['nou','en_revisio','tancat'])->default('nou')->index();
            $table->boolean('publicable')->default(false); // si el centre vol publicar-ne alguns (mur intern)
            
            // Privacitat mínima: hash per a limitar abusos/duplicates sense guardar IP/DNI en clar
            $table->string('dni_hash', 64)->nullable()->index();
             
            // Adjunt opcional
            $table->string('adjunto_path')->nullable();

            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('bustia_violeta');
    }
};
