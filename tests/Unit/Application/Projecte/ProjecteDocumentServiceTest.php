<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Projecte;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Application\Projecte\ProjecteDocumentService;
use Intranet\Entities\Alumno;
use Intranet\Entities\Projecte;
use Intranet\Entities\Reunion;
use Tests\TestCase;

class ProjecteDocumentServiceTest extends TestCase
{
    use WithoutModelEvents;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        $schema = Schema::connection('sqlite');
        $schema->dropIfExists('ordenes_reuniones');
        $schema->dropIfExists('reuniones');
        $schema->dropIfExists('projectes');
        $schema->dropIfExists('alumnos');

        $schema->create('alumnos', function (Blueprint $table): void {
            $table->string('nia')->primary();
            $table->string('nombre')->nullable();
            $table->string('apellido1')->nullable();
            $table->string('apellido2')->nullable();
            $table->timestamps();
        });

        $schema->create('projectes', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('idAlumne')->nullable();
            $table->string('grup')->nullable();
            $table->unsignedTinyInteger('estat')->default(1);
            $table->string('titol')->nullable();
            $table->text('descripcio')->nullable();
            $table->date('defensa')->nullable();
            $table->string('hora_defensa')->nullable();
            $table->timestamps();
        });

        $schema->create('reuniones', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('tipo')->nullable();
            $table->string('grupo')->nullable();
            $table->string('curso')->nullable();
            $table->unsignedInteger('numero')->nullable();
            $table->date('fecha')->nullable();
            $table->string('descripcion')->nullable();
            $table->text('objectivos')->nullable();
            $table->string('idProfesor')->nullable();
            $table->string('idEspacio')->nullable();
            $table->string('fichero')->nullable();
            $table->timestamps();
        });

        $schema->create('ordenes_reuniones', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('idReunion');
            $table->string('descripcion');
            $table->string('resumen')->nullable();
            $table->unsignedInteger('orden');
        });
    }

    public function test_create_proposal_acta_crea_reunion_i_ordenes(): void
    {
        $service = new ProjecteDocumentService();
        $projectes = collect([
            $this->makeProjecte('A001', 'Anna Test', 'Projecte 1'),
            $this->makeProjecte('A002', 'Biel Test', 'Projecte 2'),
        ]);

        $acta = $service->createProposalActa($projectes, 'P001');

        $this->assertInstanceOf(Reunion::class, $acta);
        $this->assertSame(11, (int) $acta->tipo);
        $this->assertSame('P001', (string) $acta->idProfesor);
        $this->assertDatabaseHas('ordenes_reuniones', [
            'idReunion' => $acta->id,
            'descripcion' => 'Anna Test',
            'resumen' => 'Projecte 1 (Tutor individual)',
            'orden' => 1,
        ]);
        $this->assertDatabaseHas('ordenes_reuniones', [
            'idReunion' => $acta->id,
            'descripcion' => 'Biel Test',
            'resumen' => 'Projecte 2 (Tutor individual)',
            'orden' => 2,
        ]);
    }

    public function test_create_defense_acta_crea_reunion_i_ordenes_amb_data_i_hora(): void
    {
        $service = new ProjecteDocumentService();
        $projectes = collect([
            $this->makeProjecte('A003', 'Carla Test', 'Projecte 3', '2026-05-20', '09:00'),
        ]);

        $acta = $service->createDefenseActa($projectes, 'P002');

        $this->assertSame(12, (int) $acta->tipo);
        $this->assertDatabaseHas('ordenes_reuniones', [
            'idReunion' => $acta->id,
            'descripcion' => 'Carla Test',
            'resumen' => '(Projecte 3)20-05-2026(09:00)',
            'orden' => 1,
        ]);
    }

    private function makeProjecte(
        string $nia,
        string $fullName,
        string $titol,
        ?string $defensa = null,
        ?string $horaDefensa = null
    ): Projecte {
        [$nombre, $apellido1] = explode(' ', $fullName, 2);

        $alumno = new Alumno();
        $alumno->nia = $nia;
        $alumno->nombre = $nombre;
        $alumno->apellido1 = $apellido1;
        $alumno->apellido2 = '';

        $projecte = new Projecte();
        $projecte->idAlumne = $nia;
        $projecte->titol = $titol;
        $projecte->defensa = $defensa;
        $projecte->hora_defensa = $horaDefensa;
        $projecte->setRelation('Alumno', $alumno);

        return $projecte;
    }
}
