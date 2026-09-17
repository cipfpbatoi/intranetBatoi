use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea la taula per a registar els cicles formatius que l'alumne ha cursat anteriorment.
     * Permet cicles ja extints (FK opcional a cicles).
     */
    public function up(): void
    {
        Schema::create('cicles_formatius_cursats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alumno_id')->constrained('alumnos');
            $table->foreignId('cicle_formatiu_id')->nullable()->constrained('cicles');
            $table->integer('any_curs');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cicles_formatius_cursats');
    }
};
