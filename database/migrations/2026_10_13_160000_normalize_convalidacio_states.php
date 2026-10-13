<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /** Normalitza les resolucions automàtiques antigues sense eliminar cap registre. */
    public function up(): void
    {
        DB::table('convalidacions')
            ->where('estat', 'resolta')
            ->update(['estat' => 'acceptada']);
    }

    /** La normalització de dades no és reversible sense perdre el significat actual. */
    public function down(): void
    {
        // No es revertix: no hi ha cap DELETE ni canvi d'esquema en esta migració.
    }
};
