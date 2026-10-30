<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Crea el registre append-only de les aportacions del personal. */
    public function up(): void
    {
        Schema::create('convalidacio_aportacions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('convalidacio_id');
            $table->string('actor_dni', 20);
            $table->json('rols_actor');
            $table->text('comentari')->nullable();
            $table->string('descripcio', 120)->nullable();
            $table->string('path')->nullable();
            $table->string('original_name')->nullable();
            $table->string('mime', 100)->nullable();
            $table->timestamps();

            $table->foreign('convalidacio_id')->references('id')->on('convalidacions')->cascadeOnDelete();
            $table->index('actor_dni');
        });
    }

    /** Elimina la taula d'aportacions. */
    public function down(): void
    {
        Schema::dropIfExists('convalidacio_aportacions');
    }
};
