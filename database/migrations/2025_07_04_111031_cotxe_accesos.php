<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cotxe_accessos', function (Blueprint $table) {
            $table->id();
            $table->string('matricula');
            $table->timestamp('data')->useCurrent();
            $table->boolean('autoritzat')->default(false);
            $table->boolean('porta_oberta')->default(false);
            $table->string('device')->nullable();
            $table->timestamps(); // Per created_at i updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cotxe_accessos');
    }
};
