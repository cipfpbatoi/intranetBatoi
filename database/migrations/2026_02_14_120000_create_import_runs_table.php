<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('import_runs', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->string('type', 20);
            $table->string('status', 20)->default('pending');
            $table->string('file_path');
            $table->json('options')->nullable();
            $table->unsignedTinyInteger('progress')->default(0);
            $table->text('message')->nullable();
            $table->text('error')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamps();

            $table->index(['type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('import_runs');
    }
};

