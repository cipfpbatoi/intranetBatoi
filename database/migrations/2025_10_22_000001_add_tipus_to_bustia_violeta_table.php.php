<?php
// database/migrations/2025_10_22_000001_add_tipus_to_bustia_violeta_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('bustia_violeta', function (Blueprint $table) {
            $table->enum('tipus', ['violeta','convivencia'])->default('violeta')->after('id')->index();
            $table->enum('finalitat', ['parlar','escoltar','visibilitzar'])
                  ->default('escoltar')
                  ->after('categoria')
                  ->index();
        });
    }
    public function down(): void {
        Schema::table('bustia_violeta', function (Blueprint $table) {
            $table->dropColumn('tipus');
            $table->dropColumn('finalitat');    
        });
    }
};
