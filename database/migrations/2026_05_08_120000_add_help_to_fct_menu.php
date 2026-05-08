<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Associa l'ajuda del panell operatiu de FCT al menú corresponent.
 */
return new class extends Migration
{
    /**
     * Afig l'enllaç d'ajuda a l'entrada de menú de FCT.
     */
    public function up(): void
    {
        DB::table('menus')
            ->where(function ($query): void {
                $query->whereIn('url', ['/fct', 'fct'])
                    ->orWhere('nombre', 'fct');
            })
            ->update(['ajuda' => 'manual-fct-panel.html']);
    }

    /**
     * Retira l'enllaç d'ajuda afegit per esta migració.
     */
    public function down(): void
    {
        DB::table('menus')
            ->where('ajuda', 'manual-fct-panel.html')
            ->update(['ajuda' => '']);
    }
};
