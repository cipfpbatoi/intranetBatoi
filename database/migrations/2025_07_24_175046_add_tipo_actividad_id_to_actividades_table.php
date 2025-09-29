<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTipoActividadIdToActividadesTable extends Migration
{
    public function up(): void
    {
        Schema::table('actividades', function (Blueprint $table) {
            $table->foreignId('tipo_actividad_id')
                ->nullable()
                ->constrained('tipo_actividad')
                ->nullOnDelete(); // o ->onDelete('restrict') segons la lògica que vulguis
        });
    }

    public function down(): void
    {
        Schema::table('actividades', function (Blueprint $table) {
            $table->dropForeign(['tipo_actividad_id']);
            $table->dropColumn('tipo_actividad_id');
        });
    }
}
