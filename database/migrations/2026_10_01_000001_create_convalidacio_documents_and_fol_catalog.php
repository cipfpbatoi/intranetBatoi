<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Crea el catàleg exacte FOL LOGSE i els adjunts descrits per petició. */
    public function up(): void
    {
        Schema::table('convalidacions', function (Blueprint $table) {
            $table->boolean('modulo_origen_es_fol')->nullable();
            $table->string('fol_logse_cicle')->nullable();
            $table->string('fol_logse_nivell', 2)->nullable();
        });
        Schema::create('convalidacions_moduls_fol_logse', function (Blueprint $table) {
            $table->string('codigo', 6)->primary();
            $table->string('modul');
            $table->string('cicle');
            $table->string('nivell', 2);
            $table->string('sistema', 10);
        });

        DB::table('convalidacions_moduls_fol_logse')->insert(array_map(
            static fn (array $row): array => array_combine(['codigo', 'modul', 'cicle', 'nivell', 'sistema'], $row),
            [
                ['009001', 'Formació i orientació laboral', 'Desenvolupament d\'Aplicacions Informàtiques', 'GS', 'LOGSE'],
                ['010001', 'Formació i orientació laboral', 'Administració i Finances', 'GS', 'LOGSE'],
                ['012602', 'Formació i orientació laboral', 'Gestió Administrativa', 'GM', 'LOGSE'],
                ['016001', 'Formació i orientació laboral', 'Comerç', 'GM', 'LOGSE'],
                ['028001', 'Formació i orientació laboral', 'Cures Auxiliars d\'Infermeria', 'GM', 'LOGSE'],
                ['035602', 'Formació i orientació laboral', 'Comerç Internacional', 'GS', 'LOGSE'],
                ['042602', 'Formació i orientació laboral', 'Sistemes de Regulació i Control Automàtics', 'GS', 'LOGSE'],
                ['045001', 'Formació i orientació laboral', 'Animació Sociocultural', 'GS', 'LOGSE'],
                ['049001', 'Formació i orientació laboral', 'Muntatge i Manteniment d\'Instal·lacions de Fred, Climatització i Producció de Calor', 'GM', 'LOGSE'],
                ['051001', 'Formació i orientació laboral', 'Estètica', 'GS', 'LOGSE'],
                ['055001', 'Formació i orientació laboral', 'Dietètica', 'GS', 'LOGSE'],
                ['058001', 'Formació i orientació laboral', 'Imatge', 'GS', 'LOGSE'],
                ['060001', 'Formació i orientació laboral', 'Desenvolupament de Projectes Urbanístics i Operacions Topogràfiques', 'GS', 'LOGSE'],
                ['061001', 'Formació i orientació laboral', 'Sistemes de Telecomunicació i Informàtics', 'GS', 'LOGSE'],
                ['070602', 'Formació i orientació laboral', 'Documentació Sanitària', 'GS', 'LOGSE'],
                ['072602', 'Formació i orientació laboral', 'Integració Social', 'GS', 'LOGSE'],
                ['082602', 'Formació i orientació laboral', 'Gestió i Organització dels Recursos Naturals i Paisatgístics', 'GS', 'LOGSE'],
                ['206602', 'Formació i orientació laboral', 'Caracterització', 'GM', 'LOGSE'],
                ['239602', 'Formació i orientació laboral', 'Prevenció de Riscos Professionals', 'GS', 'LOGSE'],
                ['240602', 'Formació i orientació laboral', 'Atenció Sociosanitària', 'GM', 'LOGSE'],
            ]
        ));

        Schema::create('documents_convalidacions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('convalidacio_id');
            $table->string('descripcio', 120);
            $table->string('path');
            $table->string('original_name');
            $table->string('mime', 100);
            $table->timestamps();
            $table->foreign('convalidacio_id')->references('id')->on('convalidacions')->cascadeOnDelete();
            $table->index('convalidacio_id');
        });

        DB::table('convalidacions')->whereNotNull('document_path')->orderBy('id')->each(function ($row): void {
            DB::table('documents_convalidacions')->insert([
                'convalidacio_id' => $row->id, 'descripcio' => 'Document acreditatiu',
                'path' => $row->document_path, 'original_name' => $row->document_original_name ?? basename($row->document_path),
                'mime' => $row->document_mime ?? 'application/octet-stream', 'created_at' => now(), 'updated_at' => now(),
            ]);
        });
        DB::table('convalidacions')->whereNotNull('document_prl_path')->orderBy('id')->each(function ($row): void {
            DB::table('documents_convalidacions')->insert([
                'convalidacio_id' => $row->id, 'descripcio' => 'Certificat de Prevenció de Riscos Laborals',
                'path' => $row->document_prl_path, 'original_name' => $row->document_prl_original_name ?? basename($row->document_prl_path),
                'mime' => $row->document_prl_mime ?? 'application/octet-stream', 'created_at' => now(), 'updated_at' => now(),
            ]);
        });
    }

    /** Elimina les estructures noves sense tocar les columnes llegades. */
    public function down(): void
    {
        Schema::dropIfExists('documents_convalidacions');
        Schema::dropIfExists('convalidacions_moduls_fol_logse');
        Schema::table('convalidacions', function (Blueprint $table) {
            $table->dropColumn(['modulo_origen_es_fol', 'fol_logse_cicle', 'fol_logse_nivell']);
        });
    }
};
