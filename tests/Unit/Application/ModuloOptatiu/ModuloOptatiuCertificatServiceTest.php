<?php

declare(strict_types=1);

namespace Tests\Unit\Application\ModuloOptatiu;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Intranet\Application\ModuloOptatiu\ModuloOptatiuCertificatService;
use Intranet\Entities\Modulo_grupo;
use Intranet\Entities\ModulOptatiuCertificat;
use Tests\TestCase;

class ModuloOptatiuCertificatServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        $schema = Schema::connection('sqlite');
        foreach ([
            'modul_optatiu_certificat_alumnes',
            'modul_optatiu_certificats',
            'alumno_resultados',
            'alumnos_grupos',
            'alumnos',
            'modulo_grupos',
            'modulo_ciclos',
            'modulos',
            'grupos',
        ] as $table) {
            $schema->dropIfExists($table);
        }

        $schema->create('modulos', function (Blueprint $table): void {
            $table->string('codigo', 20)->primary();
            $table->string('cliteral')->nullable();
            $table->string('vliteral')->nullable();
        });

        $schema->create('modulo_ciclos', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('idModulo', 20)->nullable();
            $table->unsignedInteger('idCiclo')->nullable();
            $table->unsignedInteger('idDepartamento')->nullable();
            $table->unsignedTinyInteger('curso')->default(1);
        });

        $schema->create('grupos', function (Blueprint $table): void {
            $table->string('codigo', 10)->primary();
            $table->unsignedInteger('idCiclo')->nullable();
            $table->string('nombre')->nullable();
            $table->string('turno', 1)->nullable();
            $table->timestamps();
        });

        $schema->create('modulo_grupos', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('idModuloCiclo')->nullable();
            $table->string('idGrupo', 10)->nullable();
        });

        $schema->create('alumnos', function (Blueprint $table): void {
            $table->string('nia', 8)->primary();
            $table->string('dni')->nullable();
            $table->string('nombre')->nullable();
            $table->string('apellido1')->nullable();
            $table->string('apellido2')->nullable();
            $table->string('email')->nullable();
            $table->string('sexo', 1)->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        $schema->create('alumnos_grupos', function (Blueprint $table): void {
            $table->string('idAlumno', 8);
            $table->string('idGrupo', 10);
        });

        $schema->create('alumno_resultados', function (Blueprint $table): void {
            $table->increments('id');
            $table->string('idAlumno', 8);
            $table->unsignedInteger('idModuloGrupo');
            $table->unsignedTinyInteger('nota')->default(0);
            $table->unsignedTinyInteger('valoraciones')->default(0);
            $table->string('observaciones', 200)->nullable();
        });

        $schema->create('modul_optatiu_certificats', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('idModuloGrupo')->unique();
            $table->string('denominacio');
            $table->string('idProfesor', 10);
            $table->timestamps();
        });

        $schema->create('modul_optatiu_certificat_alumnes', function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('idCertificat');
            $table->string('idAlumno', 8);
            $table->timestamp('enviat_at')->nullable();
            $table->timestamp('registrat_at')->nullable();
            $table->string('fitxer')->nullable();
            $table->timestamps();
        });

        DB::table('modulos')->insert([
            'codigo' => 'OPT1',
            'cliteral' => 'Optatiu',
            'vliteral' => 'Mòdul optatiu',
        ]);
        DB::table('modulo_ciclos')->insert([
            'id' => 1,
            'idModulo' => 'OPT1',
            'idCiclo' => 1,
            'idDepartamento' => 1,
            'curso' => 1,
        ]);
        DB::table('grupos')->insert([
            'codigo' => 'G1',
            'idCiclo' => 1,
            'nombre' => 'Grup 1',
            'turno' => 'M',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('modulo_grupos')->insert([
            'id' => 1,
            'idModuloCiclo' => 1,
            'idGrupo' => 'G1',
        ]);
        DB::table('alumnos')->insert([
            [
                'nia' => 'A1',
                'dni' => '11111111A',
                'nombre' => 'ALFA',
                'apellido1' => 'U',
                'apellido2' => 'UNO',
                'email' => 'a1@example.test',
                'sexo' => 'H',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nia' => 'A2',
                'dni' => '22222222B',
                'nombre' => 'BETA',
                'apellido1' => 'D',
                'apellido2' => 'DOS',
                'email' => 'a2@example.test',
                'sexo' => 'M',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
        DB::table('alumnos_grupos')->insert([
            ['idAlumno' => 'A1', 'idGrupo' => 'G1'],
            ['idAlumno' => 'A2', 'idGrupo' => 'G1'],
        ]);
    }

    public function test_guarda_les_notes_en_alumno_resultados(): void
    {
        $service = new ModuloOptatiuCertificatService();
        $certificat = ModulOptatiuCertificat::query()->create([
            'idModuloGrupo' => 1,
            'denominacio' => 'Optativa aplicada',
            'idProfesor' => 'P1',
        ]);

        $saved = $service->save($certificat, 'Optativa actualitzada', [
            'A1' => 8,
            'A2' => 9,
        ]);

        $this->assertSame(2, $saved);
        $this->assertDatabaseHas('alumno_resultados', [
            'idAlumno' => 'A1',
            'idModuloGrupo' => 1,
            'nota' => 8,
        ]);
        $this->assertDatabaseHas('modul_optatiu_certificats', [
            'id' => $certificat->id,
            'denominacio' => 'Optativa actualitzada',
        ]);
    }

    public function test_detecta_alumnat_sense_nota_abans_demetre(): void
    {
        $service = new ModuloOptatiuCertificatService();
        $certificat = ModulOptatiuCertificat::query()->create([
            'idModuloGrupo' => 1,
            'denominacio' => 'Optativa aplicada',
            'idProfesor' => 'P1',
        ]);
        DB::table('alumno_resultados')->insert([
            'idAlumno' => 'A1',
            'idModuloGrupo' => 1,
            'nota' => 7,
        ]);

        $errors = $service->validationErrors($certificat);

        $this->assertCount(1, $errors);
        $this->assertStringContainsString('BETA', $errors[0]);
    }

    public function test_reutilitza_la_nota_existent_del_mateix_modul_grup(): void
    {
        $service = new ModuloOptatiuCertificatService();
        $certificat = ModulOptatiuCertificat::query()->create([
            'idModuloGrupo' => 1,
            'denominacio' => 'Optativa aplicada',
            'idProfesor' => 'P1',
        ]);
        DB::table('alumno_resultados')->insert([
            'idAlumno' => 'A1',
            'idModuloGrupo' => 1,
            'nota' => 6,
        ]);

        $data = $service->panelData($certificat);

        $this->assertSame(6, (int) $data['resultats']->get('A1')->nota);
    }
}
