<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/** Fa visibles en el circuit habitual de faltes les resolucions ja autoritzades. */
return new class extends Migration {
    /** Vincula sense duplicar el PDF les faltes d'assumptes particulars preexistents. */
    public function up(): void
    {
        DB::table('assumptes_particulars')
            ->join('faltas', 'faltas.id', '=', 'assumptes_particulars.falta_id')
            ->where('assumptes_particulars.estat', 'autoritzada')
            ->whereNotNull('assumptes_particulars.resolucio_document')
            ->whereNull('faltas.fichero')
            ->select('faltas.id', 'assumptes_particulars.resolucio_document')
            ->orderBy('faltas.id')
            ->chunk(100, static function ($files): void {
                foreach ($files as $file) {
                    DB::table('faltas')->where('id', $file->id)->whereNull('fichero')->update([
                        'fichero' => $file->resolucio_document,
                    ]);
                }
            });
    }

    /** La vinculació no es desfà per evitar ocultar documents existents. */
    public function down(): void
    {
    }
};
