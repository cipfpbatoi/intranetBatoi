<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTipoActividadTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tipo_actividad', function (Blueprint $table) {
            $table->id(); // Clau primària
            $table->string('cliteral', 50);
            $table->string('vliteral', 50);
            $table->text('justificacio')->nullable();

            // Clau forana opcional al departament
            $table->tinyInteger('departamento_id')->nullable(); // sense unsigned!

            $table->foreign('departamento_id')
                ->references('id')
                ->on('departamentos')
                ->onDelete('restrict');
            $table->date('fecha_aprobacion')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipo_actividad');
    }
}
