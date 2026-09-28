<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Afig el codi estable i l'inferix només per a descripcions exactes conegudes.
     */
    public function up(): void
    {
        Schema::table('ordenes_reuniones', function (Blueprint $table): void {
            $table->string('codigo', 64)->nullable()->after('idReunion')->index();
        });

        foreach ($this->knownDescriptions() as $description => $code) {
            $query = DB::table('ordenes_reuniones')->whereNull('codigo');
            $driver = DB::connection()->getDriverName();

            if ($driver === 'mysql') {
                $query->whereRaw('BINARY `descripcion` = BINARY ?', [$description]);
            } elseif ($driver === 'sqlite') {
                $query->whereRaw('"descripcion" = ? COLLATE BINARY', [$description]);
            } else {
                $query->where('descripcion', $description);
            }

            $query->update(['codigo' => $code]);
        }
    }

    /**
     * Elimina el codi estable sense modificar la resta de dades dels punts.
     */
    public function down(): void
    {
        Schema::table('ordenes_reuniones', function (Blueprint $table): void {
            $table->dropIndex(['codigo']);
            $table->dropColumn('codigo');
        });
    }

    /**
     * Descripcions històriques que permeten una inferència inequívoca.
     *
     * @return array<string, string>
     */
    private function knownDescriptions(): array
    {
        return [
            'Lectura acta anterior' => 'previous_minutes_reading',
            'Torn obert de paraula' => 'open_floor',
            'Informe direcció' => 'direction_report',
            'Informe Caporalia' => 'head_studies_report',
            "Revisió d'acords adoptats a la sessió anterior" => 'previous_agreements_review',
            'Opinió i/o comentaris dels alumnes' => 'student_opinion',
            'Opinió dels alumnes' => 'student_opinion',
            'Problemes detectats al grup i mesures a prendre' => 'group_problems',
            'Problemes detectats al grup i mesures a pendre' => 'group_problems',
            'Alumnes amb dificultats acadèmiques i mesures a adoptar' => 'nese_follow_up',
            'Acords adoptats' => 'agreements',
            'Observacions' => 'observations',
            'Nº Alumnes' => 'student_count',
            'Nº Votants' => 'voter_count',
            'Candidats' => 'candidates',
            'Vots' => 'votes',
            'Delegat' => 'delegate',
            'Subdelegat' => 'deputy_delegate',
            'Secretari' => 'secretary',
            'Vocal' => 'member',
            "Revisió de l'acta de qualificacions" => 'grades_review',
            'Valoració general dels resultats obtinguts' => 'results_assessment',
        ];
    }
};
