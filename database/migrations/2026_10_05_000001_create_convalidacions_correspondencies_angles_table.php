<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Crea el catàleg privat de cicles amb anglés de cinc hores o més. */
    public function up(): void
    {
        Schema::create('convalidacions_correspondencies_angles', function (Blueprint $table): void {
            $table->id();
            $table->string('codi_cicle_angles', 50);
            $table->string('nom_cicle_angles_val', 255);
            $table->string('nom_cicle_angles_cas', 255);
            $table->string('codi_cicle_contenidor', 50);
            $table->string('nom_cicle_contenidor_val', 255);
            $table->string('nom_cicle_contenidor_cas', 255);
            $table->boolean('es_grau_superior');
            $table->timestamps();

            $table->unique(
                ['codi_cicle_angles', 'codi_cicle_contenidor'],
                'convalidacions_angles_cicles_unique'
            );
            $table->index('codi_cicle_contenidor', 'convalidacions_angles_contenidor_index');
        });
    }

    /** Elimina el catàleg de correspondències d'anglés. */
    public function down(): void
    {
        Schema::dropIfExists('convalidacions_correspondencies_angles');
    }
};
