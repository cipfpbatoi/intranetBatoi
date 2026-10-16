<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Afig el NIF separat i migra els valors llegats amb format «NIF nom».
     */
    public function up(): void
    {
        Schema::table('empresas', function (Blueprint $table): void {
            $table->string('nif_gerente', 20)->nullable()->after('gerente');
        });

        DB::table('empresas')
            ->select(['id', 'gerente', 'nif_gerente'])
            ->whereNull('nif_gerente')
            ->orderBy('id')
            ->chunkById(200, function ($empresas): void {
                foreach ($empresas as $empresa) {
                    $dades = $this->splitLegacyManager((string) $empresa->gerente);
                    if ($dades === null) {
                        continue;
                    }

                    DB::table('empresas')->where('id', $empresa->id)->update([
                        'gerente' => $dades['nom'],
                        'nif_gerente' => $dades['nif'],
                    ]);
                }
            });
    }

    /**
     * Torna a unir el NIF i el nom abans d'eliminar la columna.
     */
    public function down(): void
    {
        DB::table('empresas')
            ->select(['id', 'gerente', 'nif_gerente'])
            ->whereNotNull('nif_gerente')
            ->orderBy('id')
            ->chunkById(200, function ($empresas): void {
                foreach ($empresas as $empresa) {
                    $nif = trim((string) $empresa->nif_gerente);
                    $nom = trim((string) $empresa->gerente);
                    if ($nif === '') {
                        continue;
                    }

                    $gerente = str_starts_with(strtoupper($nom), strtoupper($nif))
                        ? $nom
                        : trim($nif . ' ' . $nom);

                    DB::table('empresas')->where('id', $empresa->id)->update(['gerente' => $gerente]);
                }
            });

        Schema::table('empresas', function (Blueprint $table): void {
            $table->dropColumn('nif_gerente');
        });
    }

    /**
     * Separa només identificadors espanyols inequívocs situats a l'inici.
     *
     * @return array{nif:string,nom:string}|null
     */
    private function splitLegacyManager(string $value): ?array
    {
        $pattern = '/^\s*(\d{8}[A-Za-z]|[XYZxyz]\d{7}[A-Za-z]|[ABCDEFGHJNPQRSUVWabcdefghjnpqrsuvw]\d{7}[0-9A-Ja-j])(?:\s*[-:]\s*|\s+)(.+?)\s*$/u';
        if (preg_match($pattern, $value, $matches) !== 1) {
            return null;
        }

        $nom = trim($matches[2]);
        if ($nom === '') {
            return null;
        }

        return [
            'nif' => strtoupper($matches[1]),
            'nom' => $nom,
        ];
    }
};
