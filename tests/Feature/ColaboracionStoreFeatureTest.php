<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Intranet\Entities\Profesor;
use Intranet\Events\ActivityReport;
use Tests\TestCase;

/**
 * Regressions de la validació d'alta de col·laboracions des d'una empresa.
 */
class ColaboracionStoreFeatureTest extends TestCase
{
    /**
     * Prepara una base de dades aïllada i un tutor autenticat.
     */
    protected function setUp(): void
    {
        parent::setUp();

        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        DB::setDefaultConnection('sqlite');

        Event::fake([ActivityReport::class]);
        $this->withoutMiddleware();
        $profesor = new Profesor();
        $profesor->setRawAttributes(['dni' => 'TUTOR001A', 'rol' => config('roles.rol.tutor')]);
        $this->actingAs($profesor, 'profesor');

        Schema::create('centros', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('idEmpresa');
        });
        Schema::create('colaboraciones', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('idCentro');
            $table->unsignedInteger('idCiclo');
            $table->string('contacto');
            $table->string('telefono');
            $table->string('email');
            $table->unsignedInteger('puestos');
            $table->string('tutor');
            $table->unsignedTinyInteger('estado');
            $table->timestamps();
        });
        DB::table('centros')->insert(['id' => 1, 'idEmpresa' => 10]);
    }

    /**
     * L'alta sense camp estat guarda el valor predeterminat «No sé».
     */
    public function test_alta_sense_estat_guarda_el_valor_inicial(): void
    {
        $this->post('/colaboracion/create', $this->dadesAlta())
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('empresa.detalle', ['empresa' => 10]));

        $this->assertDatabaseHas('colaboraciones', [
            'idCentro' => 1,
            'idCiclo' => 2,
            'estado' => 1,
            'contacto' => 'Contacte empresa',
        ]);
    }

    /**
     * Separar les regles d'alta no elimina les validacions de contacte i llocs.
     */
    public function test_alta_rebutja_dades_de_contacte_i_llocs_invalides(): void
    {
        $this->post('/colaboracion/create', array_replace($this->dadesAlta(), [
            'contacto' => '',
            'telefono' => '',
            'email' => 'correu-invalid',
            'puestos' => 'text',
        ]))->assertSessionHasErrors(['contacto', 'telefono', 'email', 'puestos'])
            ->assertSessionDoesntHaveErrors('estado');

        $this->assertDatabaseCount('colaboraciones', 0);
    }

    /**
     * L'edició continua exigint seleccionar un estat explícit.
     */
    public function test_edicio_continua_exigint_estat(): void
    {
        $this->post('/colaboracion/create', $this->dadesAlta())->assertSessionHasNoErrors();
        $id = DB::table('colaboraciones')->value('id');

        $this->put('/colaboracion/' . $id . '/edit', $this->dadesAlta())
            ->assertSessionHasErrors('estado');

        $this->assertDatabaseHas('colaboraciones', ['id' => $id, 'estado' => 1]);
    }

    /**
     * Reproduïx els camps enviats pel formulari real, que no inclou estat.
     *
     * @return array<string, int|string>
     */
    private function dadesAlta(): array
    {
        return [
            'idCentro' => 1,
            'idCiclo' => 2,
            'contacto' => 'Contacte empresa',
            'telefono' => '965000000',
            'email' => 'contacte@example.com',
            'puestos' => 2,
            'tutor' => 'Tutor Prova',
        ];
    }
}
