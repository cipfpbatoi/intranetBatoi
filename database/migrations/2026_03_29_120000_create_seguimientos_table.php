<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seguimientos', function (Blueprint $table): void {
            $table->id();
            $table->string('domain_type', 40);
            $table->string('domain_id', 20);
            $table->string('contact_type', 40);
            $table->string('title', 120);
            $table->text('comment')->nullable();
            $table->string('author_id', 10)->nullable();
            $table->dateTime('contacted_at');
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['domain_type', 'domain_id'], 'seguimientos_domain_index');
            $table->index(['contact_type', 'contacted_at'], 'seguimientos_type_contacted_index');
            $table->index('author_id', 'seguimientos_author_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seguimientos');
    }
};
