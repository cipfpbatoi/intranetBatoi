    <?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('votes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_id',8)->collation('utf8_unicode_ci')->index();
            $table->integer('option_id')->unsigned();
            $table->integer('idModuloGrupo')->unsigned();
            $table->string('idProfesor',10)->nullable();
            $table->tinyInteger('value')->nullable();
            $table->text('text')->nullable();
            $table->timestamps();

            $table->foreign('option_id')->references('id')->on('options');
            $table->foreign('user_id')->references('nia')->on('alumnos');
            $table->foreign('idModuloGrupo')->references('id')->on('modulo_grupos');
            $table->foreign('idProfesor')->references('dni')->on('profesores');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('votes');
    }
}
