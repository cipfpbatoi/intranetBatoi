<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Guarda la identificació de FOL LOGSE i el certificat PRL separat. */
    public function up(): void
    {
        Schema::table('convalidacions', function (Blueprint $table) {
            $table->boolean('fol_logse')->nullable()->after('declaracio_responsable');
            $table->string('document_prl_path')->nullable()->after('document_mime');
            $table->string('document_prl_original_name')->nullable()->after('document_prl_path');
            $table->string('document_prl_mime', 100)->nullable()->after('document_prl_original_name');
        });
    }

    /** Elimina els camps de l'acreditació PRL. */
    public function down(): void
    {
        Schema::table('convalidacions', function (Blueprint $table) {
            $table->dropColumn(['fol_logse', 'document_prl_path', 'document_prl_original_name', 'document_prl_mime']);
        });
    }
};
